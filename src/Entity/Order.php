<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    const STATUSES = ['created', 'finished', 'ordered', 'ready', 'served', 'payed'];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Type("string")
     * @Assert\Choice(choices=Order::STATUSES, message="Choose a valid status")
     * @ORM\Column(type="string", length=10)
     * @Groups({"show_orders"})
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity=Item::class, inversedBy="orders")
     */
    private $items;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"show_orders"})
     */
    private $created;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->created = new \DateTime('now', new \DateTimeZone('Europe/Ljubljana'));
        $this->status = 'created';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        $this->items->removeElement($item);

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }
}
