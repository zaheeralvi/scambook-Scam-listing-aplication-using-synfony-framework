<?php

namespace App\Entity;

/**
 * Company
 */
class ScamDetails
{
    /**
     * @var string
     */
    private $damagePrice;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $investigation =false;

    /**
     * @var string
     */
    private $abbreviation; 
    
    /**
     * @var string
     */
    private $website;

    /**
     * @return string
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }

    /**
     * @param string $abbreviation
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @var string
     */
    private $description;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @var \DateTime
     */
    private $dateOccurance;

    /**
     * @return string
     */
    public function getDamagePrice()
    {
        return $this->damagePrice;
    }

    /**
     * @param string $damagePrice
     */
    public function setDamagePrice($damagePrice)
    {
        $this->damagePrice = $damagePrice;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getInvestigation()
    {
        return $this->investigation;
    }

    /**
     * @param string $investigation
     */
    public function setInvestigation($investigation)
    {
        $this->investigation = $investigation;
    }
    

    /**
     * @return \DateTime
     */
    public function getDateOccurance()
    {
        return $this->dateOccurance;
    }

    /**
     * @param \DateTime $dateOccurance
     */
    public function setDateOccurance($dateOccurance)
    {
        $this->dateOccurance = $dateOccurance;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @var Company
     */
    private $company;


    /**
     * @var User
     */
    private $user;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @var integer
     */
    private $id;

    public function __toString(){
        return $this->id.'';
    }
}
