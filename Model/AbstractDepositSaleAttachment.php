<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Module\DepositSaleBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Klipper\Component\Model\Traits\FilePathTrait;
use Klipper\Component\Model\Traits\NameableTrait;
use Klipper\Component\Model\Traits\OrganizationalRequiredTrait;
use Klipper\Component\Model\Traits\TimestampableTrait;
use Klipper\Component\Model\Traits\UserTrackableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Deposit Sale Attachment model.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @Serializer\ExclusionPolicy("all")
 */
abstract class AbstractDepositSaleAttachment implements DepositSaleAttachmentInterface
{
    use FilePathTrait;
    use NameableTrait;
    use OrganizationalRequiredTrait;
    use TimestampableTrait;
    use UserTrackableTrait;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(max=255)
     */
    protected ?string $filePath = null;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Klipper\Module\DepositSaleBundle\Model\DepositSaleInterface",
     *     inversedBy="attachments"
     * )
     * @ORM\JoinColumn(onDelete="CASCADE")
     *
     * @Assert\NotBlank
     *
     * @Serializer\Expose
     */
    protected ?DepositSaleInterface $depositSale = null;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(max=10)
     * @Assert\NotBlank
     *
     * @Serializer\Expose
     */
    protected ?string $extension = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(max=100)
     * @Assert\NotBlank
     *
     * @Serializer\Expose
     */
    protected ?string $typeMime = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     *
     * @Serializer\Expose
     */
    protected ?int $size = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\Type(type="integer")
     *
     * @Serializer\Expose
     */
    protected ?int $width = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\Type(type="integer")
     *
     * @Serializer\Expose
     */
    protected ?int $height = null;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Assert\Type(type="boolean")
     *
     * @Serializer\Expose
     */
    protected bool $image = false;

    public function getDepositSale(): ?DepositSaleInterface
    {
        return $this->depositSale;
    }

    public function setDepositSale(?DepositSaleInterface $depositSale): self
    {
        $this->depositSale = $depositSale;

        return $this;
    }

    public function setMainAttachment(object $mainAttachment): self
    {
        if ($mainAttachment instanceof DepositSaleInterface) {
            $this->setDepositSale($mainAttachment);
        }

        return $this;
    }

    public function getMainAttachment(): ?object
    {
        return $this->getDepositSale();
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getTypeMime(): ?string
    {
        return $this->typeMime;
    }

    public function setTypeMime(?string $typeMime): self
    {
        $this->typeMime = $typeMime;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function isImage(): bool
    {
        return $this->image;
    }

    public function setImage(bool $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getFileExtension(): ?string
    {
        if (null !== $this->extension) {
            return $this->extension;
        }

        return null !== $this->filePath ? pathinfo($this->filePath, PATHINFO_EXTENSION) : null;
    }

    public function getBasename(): string
    {
        return $this->getName().'.'.$this->getFileExtension();
    }
}
