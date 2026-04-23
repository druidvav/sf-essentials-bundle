<?php

namespace Druidvav\EssentialsBundle;

use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatorAwareTrait
{
    protected TranslatorInterface $translator;

    /**
     * @required
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
}
