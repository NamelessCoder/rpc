<?php
namespace NamelessCoder\Rpc\ClientComponent\Field;

/**
 * Class TextArea
 *
 * A `<textarea>` equivalent with RTF (rich text format)
 * support, if supported by the client. The field is
 * essentially a multiline TextField with scrolling, where
 * TextField is a simple one-line-of-text style field.
 * If a client does not support rich text formatting then
 * the flag is simply ignored and the field becomes a
 * normal plaintext field.
 *
 * Note that the client may choose to limit the maximum
 * height of this field (since otherwise it could grow
 * infinitely big), but clients that do so should,
 * assuming they put any amount of focus on UX, allow the
 * field to be scrolled using normal scrolling.
 */
class TextArea extends AbstractField {

    /**
     * @var boolean
     */
    protected $richTextFormat = FALSE;

    /**
     * @return boolean
     */
    public function isRichTextFormat() {
        return $this->richTextFormat;
    }

    /**
     * @param boolean $richTextFormat
     * @return $this
     */
    public function setRichTextFormat($richTextFormat) {
        $this->richTextFormat = $richTextFormat;
        return $this;
    }

    /**
     * @return array
     */
    public function compile() {
        return array_replace(
            parent::compile(),
            array(
                'richtext' => $this->richTextFormat
            )
        );

    }

}

