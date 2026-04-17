<?php

use App\Entity\User;
use App\Entity\FAQ;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

require dirname(__FILE__).'/vendor/autoload.php';
require dirname(__FILE__).'/src/Kernel.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(dirname(__FILE__).'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();
$em = $container->get('doctrine')->getManager();

// Get services from container - we might need to make them public or get them via another way
// In Symfony 6, services are private by default. Let's try to get them.
try {
    $openAIService = $container->get(\App\Service\OpenAIService::class);
    $hfService = $container->get(\App\Service\HuggingFaceService::class);
} catch (\Exception $e) {
     // If private, we'll try to instantiate them manually for seeding
      $httpClient = new \Symfony\Component\HttpClient\NativeHttpClient();
      $cache = $container->get('cache.app');
     $openAIService = new \App\Service\OpenAIService($_ENV['OPENAI_API_KEY'], $httpClient, $cache);
     $hfService = new \App\Service\HuggingFaceService($_ENV['HUGGINGFACE_API_KEY'], $httpClient, $cache);
  }
  
  $passwordHasher = null;
  try {
      $passwordHasher = $container->get('security.user_password_hasher');
  } catch (\Exception $e) {}

echo "--- Seeding Database ---\n";

// Clear FAQs
$allFaqs = $em->getRepository(FAQ::class)->findAll();
foreach ($allFaqs as $f) $em->remove($f);
$em->flush();
echo "🗑️ Cleared existing FAQs.\n";

// 1. Create Admin User
$email = 'admin@flyandgo.tn';
$existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);

if (!$existingUser) {
    $user = new User();
    $user->setEmail($email)
         ->setNom('Admin')
         ->setPrenom('FlyAndGo')
         ->setRole('ROLE_ADMIN')
         ->setActif(true);
    
    if ($passwordHasher) {
        $hashedPassword = $passwordHasher->hashPassword($user, 'admin123');
        $user->setPassword($hashedPassword);
    } else {
        $user->setPassword(password_hash('admin123', PASSWORD_BCRYPT));
    }

    $em->persist($user);
    echo "✅ Admin user created: $email / admin123\n";
} else {
    echo "ℹ️ Admin user already exists.\n";
}

// 2. Add Sample FAQs
$faqsData = [
    [
        'q' => 'Comment réserver un hôtel ?',
        'a' => 'Pour réserver un hôtel, allez dans la section Hébergements, choisissez votre ville et vos dates, puis cliquez sur Réserver.',
        'k' => 'reservation, hotel, reserver, booking'
    ],
    [
        'q' => 'Quels sont les modes de paiement ?',
        'a' => 'Nous acceptons les cartes bancaires (Visa, Mastercard), PayPal et les virements bancaires.',
        'k' => 'paiement, carte, argent, payer'
    ],
    [
        'q' => 'Comment annuler ma réservation ?',
        'a' => 'Vous pouvez annuler votre réservation depuis votre espace client dans la section Mes Réservations.',
        'k' => 'annulation, annuler, remboursement'
    ]
];

foreach ($faqsData as $data) {
    $existingFaq = $em->getRepository(FAQ::class)->findOneBy(['question' => $data['q']]);
    if (!$existingFaq) {
        $faq = new FAQ();
        $faq->setQuestion($data['q'])
            ->setAnswer($data['a'])
            ->setKeywords($data['k']);
        
        // Generate embedding
        $textToEmbed = $faq->getQuestion() . ' ' . $faq->getKeywords();
        $embedding = null;
        
        try {
            if ($openAIService->isEnabled()) {
                echo "Attempting OpenAI embedding for: " . $data['q'] . "\n";
                $embedding = $openAIService->generateEmbedding($textToEmbed);
                if ($embedding) echo "🤖 OpenAI embedding generated successfully!\n";
                else echo "⚠️ OpenAI returned null embedding.\n";
            } elseif ($hfService->isEnabled()) {
                echo "Attempting HuggingFace embedding for: " . $data['q'] . "\n";
                $embedding = $hfService->generateEmbedding($textToEmbed);
                if ($embedding) echo "🤗 HuggingFace embedding generated successfully!\n";
                else echo "⚠️ HuggingFace returned null embedding.\n";
            } else {
                echo "❌ No AI service enabled.\n";
            }
        } catch (\Exception $e) {
            echo "❌ ERROR for " . $data['q'] . ": " . $e->getMessage() . "\n";
        }

        if ($embedding) {
            $faq->setEmbedding($embedding);
        }

        $em->persist($faq);
        echo "✅ FAQ created: " . $data['q'] . "\n";
    }
}

$em->flush();
echo "--- Seeding Completed ---\n";
