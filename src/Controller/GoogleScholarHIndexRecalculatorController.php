<?php

namespace App\Controller;

use App\Entity\Artigo;
use App\Entity\Citacao;
use App\Entity\Periodico;
use App\Repository\PeriodicoRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleScholarHIndexRecalculatorController extends AbstractController
{
    /**
     * @Route("/recalcular", name="recalcular")
     */
    public function RecalcularAutoresAction(): Response
    {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var PeriodicoRepository $periodicoRepository */
        $periodicoRepository = $em->getRepository(Periodico::class);
        $periodicos = $periodicoRepository->findByStatus(true);
        /** @var Periodico $periodico */
        foreach ($periodicos as $periodico)
        {
            $periodico->limparArtigos();
            /** @var Artigo $artigo */
            foreach ($periodico->getArtigos() as $artigo)
            {
                $artigoAutorClone = clone $artigo;
                $artigoAutorClone->resetCitacoes();
                $artigoAutorClone->setPeriodico(null);
                $artigoPeriodicoClone = clone $artigo;
                $artigoPeriodicoClone->resetCitacoes();
                $artigoPeriodicoClone->setPeriodico(null);
                $artigoAutorEPeriodicoClone = clone $artigo;
                $artigoAutorEPeriodicoClone->resetCitacoes();
                $artigoAutorEPeriodicoClone->setPeriodico(null);

                /** @var Citacao $citacao */
                foreach ($artigo->getCitacoes() as $citacao)
                {

                    $flag = true;
                    $flag2 = true;
                    /*
                     * Verifica autocitação de autor
                     */
                    foreach ($citacao->getAutores() as $autor)
                    {
                        if (!str_contains($autor,'...')){
                            $autoresTrimados = array_map('trim', $artigo->getAutores());
                            $index = array_search('...',$autoresTrimados);
                            if($index !== FALSE){
                                unset($autoresTrimados[$index]);
                            }
                            if (in_array(trim($autor), $autoresTrimados))
                            {
                                $flag = false;
                                break 1;
                            }
                        }
                    }
                    if ($flag)
                    {
                        $artigoAutorClone->addCitaco(clone $citacao);
                    }
                    /*
                     * Verifica autocitação de periodico
                     */
                    $nomePeriodico = $citacao->getPeriodicoNome();
                    $nomePeriodico = trim($nomePeriodico);
                    $nomePeriodico = preg_replace('/[0-9]+-[0-9]+/', '', $nomePeriodico);
                    $nomePeriodico = preg_replace('/([0-9]+)/', '', $nomePeriodico);
                    $nomePeriodico = preg_replace('/[0-9]+/', '', $nomePeriodico);
                    $nomePeriodico = preg_replace('/,/', '', $nomePeriodico);
                    $nomePeriodico = preg_replace('/  /', ' ', $nomePeriodico);
                    foreach ($periodico->getPossiveisNomes() as $possivelNome){
                        if (str_contains($nomePeriodico, $possivelNome)){
                            $flag2 = false;
                            break 1;
                        }
                    }
                    if ($flag2)
                    {
                        $artigoPeriodicoClone->addCitaco(clone $citacao);
                    }
                    /*
                     * Verifica autocitação de autor e periódico
                     */
                    if ($flag AND $flag2){
                        $artigoAutorEPeriodicoClone->addCitaco(clone $citacao);
                    }
                    /*
                     * Verifica todos os autores da revista
                     */

                }
                if (count($artigoAutorClone->getCitacoes()) > 0){
                    $artigoAutorClone->setNumeroCitacoes(count($artigoAutorClone->getCitacoes()));
                    $periodico->addArtigosAutore($artigoAutorClone);
                }
                if (count($artigoPeriodicoClone->getCitacoes()) > 0){
                    $artigoPeriodicoClone->setNumeroCitacoes(count($artigoPeriodicoClone->getCitacoes()));
                    $periodico->addArtigosPeriodico($artigoPeriodicoClone);
                }
                if (count($artigoAutorEPeriodicoClone->getCitacoes()) > 0){
                    $artigoAutorEPeriodicoClone->setNumeroCitacoes(count($artigoAutorEPeriodicoClone->getCitacoes()));
                    $periodico->addArtigosAutoresEPeriodico($artigoAutorEPeriodicoClone);
                }
            }

            $artigosAutorseCitacoes = array();
            foreach ($periodico->getArtigosAutores() as $artigo)
            {
                $artigosAutorseCitacoes[] = $artigo->getNumeroCitacoes();
            }
            $artigosAutoresCitacoesOrdenado = array();
            while (count($artigosAutorseCitacoes) > 0){
                $artigosAutoresCitacoesOrdenado[] = max($artigosAutorseCitacoes);
                unset($artigosAutorseCitacoes[array_search(max($artigosAutorseCitacoes),$artigosAutorseCitacoes)]);
            }
            foreach ($artigosAutoresCitacoesOrdenado as $key => $value){
                if ($key + 1 <= $value){
                    $periodico->setIndiceH5Autores($key + 1);
                }else{
                    unset($artigosAutoresCitacoesOrdenado[$key]);
                    foreach ($periodico->getArtigosAutores() as $artigo){
                        if ($artigo->getNumeroCitacoes() == $value){
                            $periodico->removeArtigosAutore($artigo);
                            break 1;
                        }
                    }
                }
            }
            $artigosPeriodicosCitacoes = array();
            foreach ($periodico->getArtigosPeriodicos() as $artigo)
            {
                $artigosPeriodicosCitacoes[] = $artigo->getNumeroCitacoes();
            }
            $artigosPeriodicosCitacoesOrdenado = array();
            while (count($artigosPeriodicosCitacoes) > 0){
                $artigosPeriodicosCitacoesOrdenado[] = max($artigosPeriodicosCitacoes);
                unset($artigosPeriodicosCitacoes[array_search(max($artigosPeriodicosCitacoes),$artigosPeriodicosCitacoes)]);
            }
            foreach ($artigosPeriodicosCitacoesOrdenado as $key => $value){
                if ($key + 1 <= $value){
                    $periodico->setIndiceH5Periodicos($key + 1);
                }else{
                    unset($artigosPeriodicosCitacoesOrdenado[$key]);
                    foreach ($periodico->getArtigosPeriodicos() as $artigo){
                        if ($artigo->getNumeroCitacoes() == $value){
                            $periodico->removeArtigosPeriodico($artigo);
                            break 1;
                        }
                    }
                }
            }
            $artigosAutoresEPeriodicosCitacoes = array();
            foreach ($periodico->getArtigosAutoresEPeriodicos() as $artigo)
            {
                $artigosAutoresEPeriodicosCitacoes[] = $artigo->getNumeroCitacoes();
            }
            $artigosAutoresEPeriodicosCitacoesOrdenado = array();
            while (count($artigosAutoresEPeriodicosCitacoes) > 0){
                $artigosAutoresEPeriodicosCitacoesOrdenado[] = max($artigosAutoresEPeriodicosCitacoes);
                unset($artigosAutoresEPeriodicosCitacoes[array_search(max($artigosAutoresEPeriodicosCitacoes),$artigosAutoresEPeriodicosCitacoes)]);
            }
            foreach ($artigosAutoresEPeriodicosCitacoesOrdenado as $key => $value){
                if ($key + 1 <= $value){
                    $periodico->setIndiceH5AutoresEPeriodicos($key + 1);
                }else{
                    unset($artigosAutoresEPeriodicosCitacoesOrdenado[$key]);
                    foreach ($periodico->getArtigosAutoresEPeriodicos() as $artigo){
                        if ($artigo->getNumeroCitacoes() == $value){
                            $periodico->removeArtigosAutoresEPeriodico($artigo);
                            break 1;
                        }
                    }
                }
            }
            if (is_null($periodico->getIndiceH5Autores())){
                $periodico->setIndiceH5Autores(0);
                $periodico->setMedianaH5Autores(0);
            }else{
                if ($periodico->getIndiceH5Autores()%2 == 0){
                    $periodico->setMedianaH5Autores(round(($artigosAutoresCitacoesOrdenado[($periodico->getIndiceH5Autores()/2)]+$artigosAutoresCitacoesOrdenado[(($periodico->getIndiceH5Autores()/2)-1)])/2));
                }else{
                    $periodico->setMedianaH5Autores($artigosAutoresCitacoesOrdenado[round($periodico->getIndiceH5Autores()/2)-1]);
                }
            }
            if (is_null($periodico->getIndiceH5Periodicos())){
                $periodico->setIndiceH5Periodicos(0);
                $periodico->setMedianaH5Periodicos(0);
            }else{
                if ($periodico->getIndiceH5Periodicos()%2 == 0){
                    $periodico->setMedianaH5Periodicos(round(($artigosPeriodicosCitacoesOrdenado[($periodico->getIndiceH5Periodicos()/2)]+$artigosPeriodicosCitacoesOrdenado[(($periodico->getIndiceH5Periodicos()/2)-1)])/2));
                }else{
                    $periodico->setMedianaH5Periodicos($artigosPeriodicosCitacoesOrdenado[round($periodico->getIndiceH5Periodicos()/2)-1]);
                }
            }
            if (is_null($periodico->getIndiceH5AutoresEPeriodicos())){
                $periodico->setIndiceH5AutoresEPeriodicos(0);
                $periodico->setMedianaH5AutoresEPeriodicos(0);
            }else{
                if ($periodico->getIndiceH5AutoresEPeriodicos()%2 == 0){
                    $periodico->setMedianaH5AutoresEPeriodicos(round(($artigosAutoresEPeriodicosCitacoesOrdenado[($periodico->getIndiceH5AutoresEPeriodicos()/2)]+$artigosAutoresEPeriodicosCitacoesOrdenado[(($periodico->getIndiceH5AutoresEPeriodicos()/2)-1)])/2));
                }else{
                    $periodico->setMedianaH5AutoresEPeriodicos($artigosAutoresEPeriodicosCitacoesOrdenado[round($periodico->getIndiceH5AutoresEPeriodicos()/2)-1]);
                }
            }
            $periodico->setStatus(false);
            $em->persist($periodico);
            $em->flush();
        }
        return $this->json([
            'message' => 'Recalculado H5',
            'path' => 'src/Controller/GoogleScholarHIndexRecalculatorController.php',
        ]);
    }
}
