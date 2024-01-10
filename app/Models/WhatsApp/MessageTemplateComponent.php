<?php

namespace App\Models\WhatsApp;

/**
 * https://documenter.getpostman.com/view/13382743/UVC2F8Rm
 */
class MessageTemplateComponent {
    /**
     * @var
     * Required.
     *
     * Values: BODY, HEADER, FOOTER, and BUTTONS. The character limit of the BODY component is 1024 characters, while the character limits of the HEADER and FOOTER components is 60 characters each.
     */
    private $type;
    /**
     * @var
     * Only applies to the HEADER type.
     *
     * Values: TEXT, IMAGE, DOCUMENT, VIDEO
     */
    private $format;
    private $text;
    private $example;
    private $buttons;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     */
    public function setFormat($format): void
    {
        $this->format = $format;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getExample()
    {
        return $this->example;
    }

    /**
     * @param mixed $example
     */
    public function setExample($example): void
    {
        $this->example = $example;
    }

    /**
     * @return mixed
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * @param mixed $buttons
     */
    public function setButtons($buttons): void
    {
        $this->buttons = $buttons;
    }

}
