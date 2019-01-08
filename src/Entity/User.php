<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();
        $arr=['ROLE_USER'];
        $this->setRoles($arr);
        $this->created_date= $this->modified_date = new \DateTime("now");
        $this->is_active=true;

        if($this->enabled == NULL){
            $this->setEnabled(1);
        }
    }

    public function __toString(){
        // return parent::getUsername();
        if(parent::getUsername() != '') {

            $return =  (string)parent::getUsername() != '' ?  parent::getUsername(). ' | ' . $this->getSalesmanName() : $this->getSalesmanName();
            $return .=  (string)$this->getFirstName() != '' ?  ' | '.$this->getFirstName() : '';
            return $return;

        }else{
            return (string) 'Create User';
        }
    }

//    /**
//     * @var string
//     */
//    protected $username;

    /**
     * @var string
     */
//    protected $usernameCanonical;

    /**
     * @var string
     */
//    protected $email;

    /**
     * @var string
     */
//    protected $emailCanonical;

    /**
     * @var boolean
     */
//    protected $enabled;
//
    /**
     * @var string
     */
//    protected $salt;

    /**
     * @var string
     */
//    protected $password;

    /**
     * @var \DateTime
     */
//    protected $lastLogin;

    /**
     * @var boolean
     */
//    protected $locked;

    /**
     * @var boolean
     */
//    protected $expired;

    /**
     * @var \DateTime
     */
//    protected $expiresAt;

    /**
     * @var string
     */
//    protected $confirmationToken;

    /**
     * @var \DateTime
     */
//    protected $passwordRequestedAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserRole")
     * @ORM\JoinColumn(name="user_role_id", referencedColumnName="id")
     */
//    protected $role;



    /**
     * @var boolean
     */
//    protected $credentialsExpired;

    /**
     * @var \DateTime
     */
//    protected $credentialsExpireAt;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $salesmanName;

    /**
     * @var \DateTime
     */
    protected $dob;

    /**
     * @var string
     */
    protected $age;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var boolean
     */
    protected $userStatus;

    /**
     * @var string
     */
    protected $facebookId;
    public function serialize()
    {
        return serialize(array($this->facebookId, parent::serialize()));
    }

    public function unserialize($data)
    {
        list($this->facebookId, $parentData) = unserialize($data);
        parent::unserialize($parentData);
    }
    /**
     * @var string
     */
    protected $googleplusId;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $curAddress;

    /**
     * @var string
     */
    protected $longitude;

    /**
     * @var string
     */
    protected $latitude;

    /**
     * @var \DateTime
     */
    protected $createdDate;

    /**
     * @var \DateTime
     */
    protected $modifiedDate;

    /**
     * @var string
     */
    protected $imageName;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $deviceId;

    /**
     * @var string
     */
    protected $contactNo;


    /**
     * @var integer
     */
    protected $id;

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getSalesmanName()
    {
        return $this->salesmanName;
    }

    /**
     * @param string salesmanName
     */
    public function setSalesmanName($salesmanName)
    {
        $this->salesmanName = $salesmanName;
    }


    /**
     * @var \App\Entity\UserType
     */
    protected $type;

    /**
     * @var \App\Entity\UserRole
     */
    protected $userRole;

    /**
     * @var \App\Entity\Gender
     */
    protected $gender;

    /**
     * @var \App\Entity\PolicyTypes
     */
    protected $policyType;

    /**
     * @var int
     */
    protected $policyAmount;

    /**
     * @var string
     */
    protected $skype;

    /**
     * @var string
     */
    protected $viber;

    /**
     * @var string
     */
    protected $whatsapp;

    /**
     * @var string
     */
    protected $dialer;

    /**
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * @param string $skype
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;
    }

    /**
     * @return string
     */
    public function getViber()
    {
        return $this->viber;
    }

    /**
     * @param string $viber
     */
    public function setViber($viber)
    {
        $this->viber = $viber;
    }

    /**
     * @return string
     */
    public function getWhatsapp()
    {
        return $this->whatsapp;
    }

    /**
     * @param string $whatsapp
     */
    public function setWhatsapp($whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    /**
     * @return string
     */
    public function getDialer()
    {
        return $this->dialer;
    }

    /**
     * @param string $dialer
     */
    public function setDialer($dialer)
    {
        $this->dialer = $dialer;
    }


    /**
     * @return mixed
     */
    public function getPolicyAmount()
    {
        return $this->policyAmount;
    }

    /**
     * @param mixed $policyAmount
     */
    public function setPolicyAmount($policyAmount)
    {
        $this->policyAmount = $policyAmount;
    }

    /**
     * @return PolicyTypes
     */
    public function getPolicyType()
    {
        return $this->policyType;
    }

    /**
     * @param PolicyTypes $policyType
     */
    public function setPolicyType($policyType)
    {
        $this->policyType = $policyType;
    }

    /**
     * @return \DateTime
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

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
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param string $facebookId
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }

    /**
     * @return string
     */
    public function getGoogleplusId()
    {
        return $this->googleplusId;
    }

    /**
     * @param string $googleplusId
     */
    public function setGoogleplusId($googleplusId)
    {
        $this->googleplusId = $googleplusId;
    }

    /**
     * @return boolean
     */
    public function isUserStatus()
    {
        return $this->userStatus;
    }

    /**
     * @param boolean $userStatus
     */
    public function setUserStatus($userStatus)
    {
        $this->userStatus = $userStatus;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return Gender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param Gender $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @param \DateTime $dob
     */
    public function setDob($dob)
    {
        $this->dob = $dob;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }
    /**
     * Set first_name
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get first_name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }




    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Get expired
     *
     * @return boolean
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Get credentialsExpired
     *
     * @return boolean
     */
    public function getCredentialsExpired()
    {
        return $this->credentialsExpired;
    }

    /**
     * Get credentialsExpireAt
     *
     * @return \DateTime
     */
    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }

    /**
     * Set age
     *
     * @param string $age
     * @return User
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return string
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Get userStatus
     *
     * @return boolean
     */
    public function getUserStatus()
    {
        return $this->userStatus;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return User
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set modifiedDate
     *
     * @param \DateTime $modifiedDate
     * @return User
     */
    public function setModifiedDate($modifiedDate)
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }

    /**
     * Get modifiedDate
     *
     * @return \DateTime
     */
    public function getModifiedDate()
    {
        return $this->modifiedDate;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @var string
     */
    private $others;


    /**
     * Set others
     *
     * @param string $others
     * @return User
     */
    public function setOthers($others)
    {
        $this->others = $others;

        return $this;
    }

    /**
     * Get others
     *
     * @return string
     */
    public function getOthers()
    {
        return $this->others;
    }

    /**
     * @param Array
     */
    public function setFBData($fbdata)
    {
        if (isset($fbdata['id'])) {
            $this->setFacebookId($fbdata['id']);
            $this->addRole('ROLE_FACEBOOK');
        }
        if (isset($fbdata['first_name'])) {
            $this->setFirstname($fbdata['first_name']);
        }
        if (isset($fbdata['last_name'])) {
            $this->setLastname($fbdata['last_name']);
        }
        if (isset($fbdata['email'])) {
            $this->setEmail($fbdata['email']);
        }
    }

     /**
     * @var string
     */
    private $postalCode;


    /**
     * Set postalCode
     *
     * @param string $postalCode
     * @return User
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }
    /**
     * @var string
     */
    private $country;


    /**
     * Set country
     *
     * @param string $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }
    /**
     * @var string
     */
    private $twitterId;

    /**
     * @var string
     */
    private $address1;

    /**
     * @var string
     */
    private $address2;


    /**
     * Set twitterId
     *
     * @param string $twitterId
     * @return User
     */
    public function setTwitterId($twitterId)
    {
        $this->twitterId = $twitterId;

        return $this;
    }

    /**
     * Get twitterId
     *
     * @return string
     */
    public function getTwitterId()
    {
        return $this->twitterId;
    }

    /**
     * Set address1
     *
     * @param string $address1
     * @return User
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get address1
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     * @return User
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }
    /**
     * @var string
     */
    private $confirmationCode;


    /**
     * Set confirmationCode
     *
     * @param string $confirmationCode
     * @return User
     */
    public function setConfirmationCode($confirmationCode)
    {
        $this->confirmationCode = $confirmationCode;

        return $this;
    }

    /**
     * Get confirmationCode
     *
     * @return string
     */
    public function getConfirmationCode()
    {
        return $this->confirmationCode;
    }
    /**
     * @var integer
     */
    private $userFrequencyId;


    /**
     * Set userFrequencyId
     *
     * @param integer $userFrequencyId
     * @return User
     */
    public function setUserFrequencyId($userFrequencyId)
    {
        $this->userFrequencyId = $userFrequencyId;

        return $this;
    }

    /**
     * Get userFrequencyId
     *
     * @return integer
     */
    public function getUserFrequencyId()
    {
        return $this->userFrequencyId;
    }
    /**
     * @var string
     */
    private $language;


    /**
     * Set language
     *
     * @param string $language
     * @return User
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @var boolean
     */
    private $isOtpVerified;


    /**
     * Set isOtpVerified
     *
     * @param boolean $isOtpVerified
     *
     * @return User
     */
    public function setIsOtpVerified($isOtpVerified)
    {
        $this->isOtpVerified = $isOtpVerified;

        return $this;
    }

    /**
     * Get isOtpVerified
     *
     * @return boolean
     */
    public function getIsOtpVerified()
    {
        return $this->isOtpVerified;
    }
    /**
     * @var integer
     */
    private $pushBadgeCount = '0';


    /**
     * Set pushBadgeCount
     *
     * @param integer $pushBadgeCount
     *
     * @return User
     */
    public function setPushBadgeCount($pushBadgeCount)
    {
        $this->pushBadgeCount = $pushBadgeCount;

        return $this;
    }

    /**
     * Get pushBadgeCount
     *
     * @return integer
     */
    public function getPushBadgeCount()
    {
        return $this->pushBadgeCount;
    }

    /**
     * DRIVER RELATED ATTRIBUTES
     */
    /**
     * @var string
     */
    protected $_licenceNumber;

    /**
     * Get licenceNumber
     *
     * @return string
     */
    public function getLicenceNumber()
    {
        return $this->_licenceNumber;
    }

    /**
     * Set licenceNumber
     *
     * @param integer $licenceNumber
     *
     * @return User
     */
    public function setLicenceNumber($licenceNumber)
    {
        $this->_licenceNumber = $licenceNumber;
        return $this;
    }

    /*
     * @var App\Entity\Cars
     */
    protected $driverCar;

    /**
     * @return Cars
     */
    public function getDriverCar()
    {
        return $this->driverCar;
    }

    /**
     * @param mixed User
     */
    public function setDriverCar($driverCar)
    {
        $this->driverCar = $driverCar;
        return $this;
    }

     /**
     * @var integer
     */
    private $designationId;

    /**
     * @var string
     */
    private $permissions;

    /**
     * @var integer
     */
    private $reportUserId;


    /**
     * Set designationId
     *
     * @param integer $designationId
     *
     * @return User
     */
    public function setDesignationId($designationId)
    {
        $this->designationId = $designationId;

        return $this;
    }

    /**
     * Get designationId
     *
     * @return integer
     */
    public function getDesignationId()
    {
        return $this->designationId;
    }

    /**
     * Set permissions
     *
     * @param string $permissions
     *
     * @return User
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Get permissions
     *
     * @return string
     */
    public function getPermissions()
    {
        return $this->permissions;
    }


}
