<?php

namespace App\Entity;

use App\Repository\SmsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=SmsRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Sms implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $number;

    /**
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tries;

    /**
     * @ORM\OneToMany(targetEntity=History::class, mappedBy="sms", orphanRemoval=true)
     */
    private $history;

    public function __construct()
    {
        $this->histories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    
    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function getStatusString(): ?string
    {
        $statuses = [
            0 => 'queued',
            1 => 'sending',
            2 => 'sent',
            3 => 'failed',
        ];
        
        return isset($statuses[$this->status]) ? $statuses[$this->status] : '';
    }

    public function getSenders()
    {
        return [
            'http://localhost:81/sms/send?',
            'http://localhost:82/sms/send?',
        ];
    }

    public function getSender(): ?string
    {
        $senders = $this->getSenders();

        if ($this->getTries() >= 0 && $this->getTries() < count($senders)) {
            return $senders[$this->getTries()];
        }
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
    */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            "number" => $this->getNumber(),
            "body" => $this->getBody(),
            "tries" => $this->getTries(),
            "status" => $this->getStatusString(),
            "created_at" => $this->getCreatedAt(),
            "updated_at" => $this->getUpdatedAt(),
        ];
    }

    public function getTries(): ?int
    {
        return $this->tries;
    }

    public function setTries(?int $tries): self
    {
        $this->tries = $tries;

        return $this;
    }

    public function retry(): self
    {
        $this->tries = $this->tries + 1 > 2 ? 2 : $this->tries + 1;
        return $this;
    }

    /**
     * @return Collection|History[]
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->setSms($this);
        }

        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getSms() === $this) {
                $history->setSms(null);
            }
        }

        return $this;
    }
}
