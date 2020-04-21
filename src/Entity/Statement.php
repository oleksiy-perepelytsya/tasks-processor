<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Table(name="statement", uniqueConstraints=@ORM\UniqueConstraint(name="id_UNIQUE", columns={"id"}))
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\StatementRepository")
 */
class Statement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer", nullable=false)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=500)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="text", type="string", length=2000)
     */
    private $text;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

       /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }


    public function toArray(){
        return [
            'id' => $this->getId(),
            'userId' => $this->getNumber(),
            'timestamp' => $this->getTitle(),
            'text' => $this->getText(),
        ];
    }

}