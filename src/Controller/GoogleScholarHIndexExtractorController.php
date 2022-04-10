<?php

namespace App\Controller;

use App\Entity\Artigo;
use App\Entity\Citacao;
use App\Entity\Periodico;
use Goutte\Client;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleScholarHIndexExtractorController extends AbstractController
{
    /**
     * @Route("/google-scholar-h-index-extractor", name="google_scholar_h_index_extractor")
     */
    public function index(LoggerInterface $logger): Response
    {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        $periodicoRepository = $this->getDoctrine()->getRepository(Periodico::class);
        $periodicos = $periodicoRepository->findByStatus(false);
        /** @var Periodico $periodico */
        foreach ($periodicos as $periodico) {
            $client = new Client();
            $sleep = rand(8,13);
            $logger->info("Dormindo por:" . $sleep);
            sleep($sleep);
            $logger->info("Lendo URL:" . $periodico->getUrl());
            $periodicocrawler = $client->request('GET', $periodico->getUrl());
            $periodicoStatus = $periodicocrawler->filter('.gsc_mlhd_list li span')->each(function (Crawler $node, $i) {
                return $node->text();
            });
            $periodico->limparArtigosColetaInicial();
            $periodico->setIndiceH5($periodicoStatus[0]);
            $periodico->setMedianaH5($periodicoStatus[1]);
            $resultado = $this->findArtigos($client, 0, $periodico->getUrl(), $logger);
            if (!is_null($resultado['periodicoPossiveisNomes'])){
                $periodico->setPossiveisNomes($resultado['periodicoPossiveisNomes']);
                /** @var Artigo $artigo */
                foreach ($resultado['artigos'] as $artigo) {
                    $periodico->addArtigo($artigo);
                    $artigo->setPeriodico($periodico);
                }
                if ($periodico->getIndiceH5() != count($resultado['artigos'])) {
                    $periodico->setIndiceH5(null);
                    $periodico->setStatus(null);
                }else{
                    $periodico->setStatus(true);
                }
            }else{
                $periodico->setStatus(null);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($periodico);
            $em->flush();
        }
        return new Response('<html><body>OK</body></html>');

    }
    /**
     * @param Client $client
     * @param int $contagem
     * @param string $periodicoURL
     * @return array|null
     */
    private function findArtigos(Client $client, int $contagem, string $periodicoURL, LoggerInterface $logger){

        $sleep = rand(8,13);
        $logger->info("Dormindo por:" . $sleep);
        sleep($sleep);
        $logger->info("LENDO URL:" . $periodicoURL . '&vq=en&cstart=' . $contagem);
        $periodicoCrawler = $client->request('GET', $periodicoURL . '&vq=en&cstart=' . $contagem);
        $artigosNodes = $periodicoCrawler->filterXPath('//td[@class="gsc_mpat_t"]')->each(function (Crawler $node, $i){
            return $node;
        });

        if (sizeof($artigosNodes) > 0){
            $artigos = array();
            $periodicoPossiveisNomes = array();
            /** @var Crawler $artigoNode */
            foreach ($artigosNodes as $artigoNode){
                $artigo = new Artigo();
                $artigo->setTitulo($artigoNode->filter('.gsc_mpat_ttl')->filter('a')->text());
                $artigo->setAutores(explode(',', trim($artigoNode->filter('.gs_gray')->first()->text())));
                if ($artigoNode->filter('.gs_gray')->count() > 1){
                    $periodicoNomeSujo = $artigoNode->filter('.gs_gray')->eq(1)->text();
                    $pedacos = explode(' ', html_entity_decode(str_replace("&nbsp;", " ", htmlentities($periodicoNomeSujo, null, 'utf-8'))));
                    $periodicoNome = null;
                    foreach ($pedacos as $pedaco){
                        if (is_numeric($pedaco) OR str_contains('…', $pedaco)){
                            break 1;
                        }
                        $periodicoNome = trim($periodicoNome . " " . $pedaco);
                    }
                    $artigo->setPeriodicoNome($periodicoNome);
                    if (!(in_array($periodicoNome, $periodicoPossiveisNomes))){
                        $periodicoPossiveisNomes[] = $periodicoNome;
                    }
                }
                $artigo->setNumeroCitacoes(intval($artigoNode->parents()->filter('.gsc_mpat_c')->text()));
                $artigo->setAno(intval($artigoNode->parents()->filter('.gsc_mpat_y')->text()));
                $citacoesURL = "https://scholar.google.com.br/" . $artigoNode->parents()->filter('.gsc_mpat_c a')->attr('href');
                $citacoes = $this->findCitacoes($client,0, $citacoesURL, $logger);
                /** @var Citacao $citacao */
                foreach ($citacoes as $citacao){
                    $artigo->addCitaco($citacao);
                    $citacao->setArtigo($artigo);
                }
                if ($artigo->getNumeroCitacoes() != count($citacoes)){
                    dump($artigo->getNumeroCitacoes() , count($citacoes),"CRAWLER PAROU DE FUNCIONAR!");die;
                }
                $artigos[] = $artigo;
            }
            $resultado = ($contagem >= 980) ? null : $this->findArtigos($client, $contagem+20, $periodicoURL, $logger);
            if (is_null($resultado)){
                return array(
                    'artigos' => $artigos,
                    'periodicoPossiveisNomes' => $periodicoPossiveisNomes
                );
            } else{
                foreach ($resultado['periodicoPossiveisNomes'] as $periodicoPossivelNome){
                    if (!(in_array($periodicoPossivelNome, $periodicoPossiveisNomes))){
                        $periodicoPossiveisNomes[] = $periodicoPossivelNome;
                    }
                }
                return array(
                    'artigos' => array_merge($artigos,$resultado['artigos']),
                    'periodicoPossiveisNomes' => $periodicoPossiveisNomes
                );
            }
        }else{
            return null;
        }
    }

    /**
     * @param Client $client
     * @param int $contagem
     * @param string $citacoesURL
     * @return array|null
     */
    private function findCitacoes(Client $client, int $contagem, string $citacoesURL, LoggerInterface $logger)
    {
        $sleep = rand(8,13);
        $logger->info("Dormindo por:" . $sleep);
        sleep($sleep);
        $logger->info("LENDO URL:" . $citacoesURL . '&vq=en&cstart=' . $contagem);
        $citacoesCrawler = $client->request('GET', $citacoesURL . '&vq=en&cstart=' . $contagem);
        $citacoesNodes = $citacoesCrawler->filterXPath('//td[@class="gsc_mpat_t"]')->each(function (Crawler $node, $i) {
            return $node;
        });
        if (sizeof($citacoesNodes) > 0) {
            $citacoes = array();
            /** @var Crawler $citacoeNode */
            foreach ($citacoesNodes as $citacaoNode){
                $citacao = New Citacao();
                $citacao->setTitulo($citacaoNode->filter('.gsc_mpat_ttl')->filter('a')->text());
                $citacao->setAutores(explode(',', trim($citacaoNode->filter('.gs_gray')->first()->text())));
                if ($citacaoNode->filter('.gs_gray')->count() > 1){
                    $periodicoNomeSujo = $citacaoNode->filter('.gs_gray')->eq(1)->text();
                    $pedacos = explode(' ', html_entity_decode(str_replace("&nbsp;", " ", htmlentities($periodicoNomeSujo, null, 'utf-8'))));
                    $periodicoNome = null;
                    foreach ($pedacos as $pedaco){
                        if (is_numeric($pedaco) OR str_contains('…', $pedaco)){
                            break 1;
                        }
                        $periodicoNome = trim($periodicoNome . " " . $pedaco);
                    }
                    $citacao->setPeriodicoNome($periodicoNome);
                }
                $citacao->setAno(intval($citacaoNode->parents()->filter('.gsc_mpat_y')->text()));
                $citacoes[] = $citacao;
            }
            $resultado = ($contagem >= 980) ? null :  $this->findCitacoes($client, $contagem+20, $citacoesURL, $logger);
            return (is_null($resultado)) ? $citacoes : array_merge($citacoes,$resultado);
        } else {
            return null;
        }
    }
}