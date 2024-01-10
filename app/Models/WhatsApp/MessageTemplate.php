<?php

namespace App\Models\WhatsApp;

/**
 * https://documenter.getpostman.com/view/13382743/UVC2F8Rm
 */
class MessageTemplate {
    /**
     * @var
     * Required.
     *
     * Values: ACCOUNT_UPDATE, PAYMENT_UPDATE, PERSONAL_FINANCE_UPDATE, SHIPPING_UPDATE, RESERVATION_UPDATE, ISSUE_RESOLUTION, APPOINTMENT_UPDATE, TRANSPORTATION_UPDATE, TICKET_UPDATE, ALERT_UPDATE, AUTO_REPLY
     */
    private $category;
    /**
     * @var
     * Required.
     *
     * The parts of the message template.
     */
    private $components;
    /**
     * @var
     * Required.
     *
     * The name of the message template.
     */
    private $name;
    /**
     * @var
     * Required.
     *
     * The language of the message template.
     */
    private $language;

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * @param mixed $components
     */
    public function setComponents($components): void
    {
        $this->components = $components;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language): void
    {
        $this->language = $language;
    }
}
