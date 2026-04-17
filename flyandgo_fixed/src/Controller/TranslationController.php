<?php

namespace App\Controller;

use App\Service\TranslationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TranslationController extends AbstractController
{
    public function __construct(
        private TranslationService $translationService
    ) {}

    #[Route('/api/translate', name: 'api_translate', methods: ['POST'])]
    public function translate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $text = $data['text'] ?? '';
        $targetLang = $data['target_lang'] ?? 'EN';
        $sourceLang = $data['source_lang'] ?? null;
        
        if (empty($text)) {
            return $this->json(['error' => 'Text is required'], 400);
        }

        $sourceLang = $sourceLang ?: $this->translationService->detectLanguage($text);
        $translated = $this->translationService->translate($text, $targetLang, $sourceLang);

        return $this->json([
            'success' => true,
            'original' => $text,
            'translated' => $translated,
            'source_lang' => $sourceLang,
            'target_lang' => $targetLang,
        ]);
    }

    #[Route('/api/translate/detect', name: 'api_translate_detect', methods: ['POST'])]
    public function detect(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';
        
        $lang = $this->translationService->detectLanguage($text);
        
        return $this->json([
            'success' => true,
            'language' => $lang,
        ]);
    }

    #[Route('/api/translate/batch', name: 'api_translate_batch', methods: ['POST'])]
    public function translateBatch(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $texts = $data['texts'] ?? [];
        $targetLang = $data['target_lang'] ?? 'EN';
        $sourceLang = $data['source_lang'] ?? null;
        
        if (empty($texts)) {
            return $this->json(['error' => 'Texts array is required'], 400);
        }

        $translations = $this->translationService->translateMultiple($texts, $targetLang, $sourceLang);

        return $this->json([
            'success' => true,
            'translations' => $translations,
        ]);
    }

    #[Route('/api/translate/avis', name: 'api_translate_avis', methods: ['POST'])]
    public function translateAvis(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $avis = $data['avis'] ?? [];
        $targetLang = $data['target_lang'] ?? 'EN';
        
        if (empty($avis)) {
            return $this->json(['error' => 'Avis array is required'], 400);
        }

        $translated = $this->translationService->translateAvis($avis, $targetLang);

        return $this->json([
            'success' => true,
            'translated_avis' => $translated,
        ]);
    }

    #[Route('/api/translate/languages', name: 'api_translate_languages', methods: ['GET'])]
    public function languages(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'languages' => $this->translationService->getSupportedLanguages(),
            'browser_lang' => $this->translationService->getBrowserLanguage(),
        ]);
    }

    #[Route('/api/translate/status', name: 'api_translate_status', methods: ['GET'])]
    public function status(): JsonResponse
    {
        return $this->json([
            'enabled' => $this->translationService->isEnabled(),
        ]);
    }
}