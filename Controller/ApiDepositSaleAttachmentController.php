<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Module\DepositSaleBundle\Controller;

use Klipper\Bundle\ApiBundle\Controller\ControllerHelper;
use Klipper\Component\Content\ContentManagerInterface;
use Klipper\Component\SecurityOauth\Scope\ScopeVote;
use Klipper\Module\DepositSaleBundle\Model\DepositSaleAttachmentInterface;
use Klipper\Module\DepositSaleBundle\Model\DepositSaleInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     path="/deposit_sale_attachments"
 * )
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class ApiDepositSaleAttachmentController
{
    /**
     * Upload the file of deposit sale attachment.
     *
     * @Entity("depositSaleId", class="App:DepositSale")
     *
     * @Route("/upload/{depositSaleId}", methods={"POST"})
     */
    public function uploadFile(
        ControllerHelper $helper,
        ContentManagerInterface $contentManager,
        DepositSaleInterface $depositSaleId
    ): Response {
        if (class_exists(ScopeVote::class)) {
            $helper->denyAccessUnlessGranted(new ScopeVote('meta/deposit_sale'));
        }

        return $contentManager->upload('deposit_sale_attachment', $depositSaleId);
    }

    /**
     * Download the file of deposit sale attachment.
     *
     * @Entity("id", class="App:DepositSaleAttachment")
     *
     * @Route("/{id}/download", methods={"GET"})
     */
    public function download(
        ControllerHelper $helper,
        ContentManagerInterface $contentManager,
        DepositSaleAttachmentInterface $id
    ): Response {
        if (class_exists(ScopeVote::class)) {
            $helper->denyAccessUnlessGranted(new ScopeVote('meta/deposit_sale'));
        }

        return $contentManager->download(
            'deposit_sale_attachment',
            $id->getFilePath(),
            $id->getBasename()
        );
    }

    /**
     * Download the image preview of deposit sale attachment.
     *
     * @Entity("id", class="App:DepositSaleAttachment")
     *
     * @Route("/{id}/download.{ext}", methods={"GET"})
     */
    public function downloadPreview(
        ControllerHelper $helper,
        ContentManagerInterface $contentManager,
        DepositSaleAttachmentInterface $id
    ): Response {
        if (class_exists(ScopeVote::class)) {
            $helper->denyAccessUnlessGranted(new ScopeVote('meta/deposit_sale'));
        }

        return $contentManager->downloadImage(
            'deposit_sale_attachment',
            $id->getFilePath(),
            $id->getBasename()
        );
    }
}
