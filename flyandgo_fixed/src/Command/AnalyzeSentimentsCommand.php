<?php

namespace App\Command;

use App\Service\SentimentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AnalyzeSentimentsCommand extends Command
{
    protected static $defaultName = 'app:analyze-sentiments';

    public function __construct(
        private EntityManagerInterface $em,
        private ?SentimentService $sentimentService = null
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->sentimentService) {
            $output->writeln('<error>SentimentService not available</error>');
            return Command::FAILURE;
        }

        $output->writeln('Analyzing sentiments for all reviews...');

        $conn = $this->em->getConnection();

        // Hebergement Avis
        $avis = $conn->fetchAllAssociative('SELECT * FROM avis_hebergement WHERE sentiment_label IS NULL OR sentiment_label = ""');
        foreach ($avis as $a) {
            $text = $a['commentaire'] ?? '';
            if ($text) {
                $result = $this->sentimentService->analyze($text);
                $conn->executeStatement(
                    'UPDATE avis_hebergement SET sentiment_score = ?, sentiment_label = ?, sentiment_stars = ?, sentiment_confidence = ?, sentiment_category = ? WHERE id_avis_hebergement = ?',
                    [$result['score'], $result['label'], $result['stars'], $result['confidence'], $result['category'], $a['id_avis_hebergement']]
                );
                $output->writeln("Updated avis_hebergement #{$a['id_avis_hebergement']}: {$result['label']}");
            }
        }

        // Circuit Avis
        $circuitAvis = $conn->fetchAllAssociative('SELECT * FROM circuit_avis WHERE (sentiment_label IS NULL OR sentiment_label = "") AND comment IS NOT NULL');
        foreach ($circuitAvis as $a) {
            $text = $a['comment'] ?? '';
            if ($text) {
                $result = $this->sentimentService->analyze($text);
                $conn->executeStatement(
                    'UPDATE circuit_avis SET sentiment_score = ?, sentiment_label = ?, sentiment_stars = ?, sentiment_confidence = ?, sentiment_category = ? WHERE id = ?',
                    [$result['score'], $result['label'], $result['stars'], $result['confidence'], $result['category'], $a['id']]
                );
                $output->writeln("Updated circuit_avis #{$a['id']}: {$result['label']}");
            }
        }

        // Activity Reviews (Review)
        $reviews = $conn->fetchAllAssociative('SELECT * FROM review WHERE (sentiment_label IS NULL OR sentiment_label = "") AND comment IS NOT NULL');
        foreach ($reviews as $r) {
            $text = $r['comment'] ?? '';
            if ($text) {
                $result = $this->sentimentService->analyze($text);
                $conn->executeStatement(
                    'UPDATE review SET sentiment_score = ?, sentiment_label = ?, sentiment_stars = ?, sentiment_confidence = ?, sentiment_category = ? WHERE id = ?',
                    [$result['score'], $result['label'], $result['stars'], $result['confidence'], $result['category'], $r['id']]
                );
                $output->writeln("Updated review #{$r['id']}: {$result['label']}");
            }
        }

        $output->writeln('<info>Done!</info>');
        return Command::SUCCESS;
    }
}