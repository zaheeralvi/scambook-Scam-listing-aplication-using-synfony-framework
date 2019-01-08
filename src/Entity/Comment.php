<?php

namespace App\Entity;

/**
 * Company
 */
class Comment
{
    /**
     * @var string
     */
    private $commentDetail;

    /**
     * @return string
     */
    public function getCommentDetail()
    {
        return $this->commentDetail;
    }

    /**
     * @param string $commentDetail
     */
    public function setCommentDetail($commentDetail)
    {
        $this->commentDetail = $commentDetail;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

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
     * @return ScamDetails
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param ScamDetails $post
     */
    public function setPost($post)
    {
        $this->post = $post;
    }

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var User
     */
    private $user;
    /**
     * @var ScamDetails
     */
    private $post;
    /**
     * @var integer
     */
    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    public function __toString(){
        return $this->id.'';
    }
}
