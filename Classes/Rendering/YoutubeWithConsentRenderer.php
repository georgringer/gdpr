<?php

namespace GeorgRinger\Gdpr\Rendering;

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\Rendering\YouTubeRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class YoutubeWithConsentRenderer extends YouTubeRenderer
{

    const DEFAULT_TEMPLATE = 'EXT:gdpr/Resources/Private/Templates/Rendering/Youtube.html';

    public function getPriority()
    {
        return 2;
    }

    public function canRender(FileInterface $file)
    {
        return TYPO3_MODE === 'FE' && parent::canRender($file);
    }


    public function render(FileInterface $file, $width, $height, array $options = null, $usedPathsRelativeToCurrentScript = false)
    {
        $uniqueId = uniqid('', true);
        $htmlCode = parent::render($file, $width, $height, $options, $usedPathsRelativeToCurrentScript);
        $htmlCode = str_replace('<iframe src="', '<iframe style="display:none" id="iframe-' . $uniqueId . '" data-src="', $htmlCode);

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $templatePath = isset($options['gdpr-youtube-template']) ? $options['gdpr-youtube-template'] : self::DEFAULT_TEMPLATE;
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templatePath));
        $view->assignMultiple([
            'width' => $width,
            'height' => $height,
            'uniqueId' => $uniqueId,
            'html' => $htmlCode,
            'file' => $file,
            'options' => $options
        ]);
        $htmlCode = $view->render();

        return $htmlCode;
    }
}
