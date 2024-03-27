<?php
namespace Druidvav\EssentialsBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Bootstrap5FormExtension extends AbstractExtension
{
    private string $style = '';
    private string $colSize = 'lg';
    private int $widgetCol = 10;
    private int $labelCol = 2;

    private array $settingsStack = [ ];

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return array(
            new TwigFunction('bs5_set_style', array($this, 'setStyle')),
            new TwigFunction('bs5_get_style', array($this, 'getStyle')),
            new TwigFunction('bs5_set_col_size', array($this, 'setColSize')),
            new TwigFunction('bs5_get_col_size', array($this, 'getColSize')),
            new TwigFunction('bs5_set_widget_col', array($this, 'setWidgetCol')),
            new TwigFunction('bs5_get_widget_col', array($this, 'getWidgetCol')),
            new TwigFunction('bs5_set_label_col', array($this, 'setLabelCol')),
            new TwigFunction('bs5_get_label_col', array($this, 'getLabelCol')),
            new TwigFunction('bs5_backup_form_settings', array($this, 'backupFormSettings')),
            new TwigFunction('bs5_restore_form_settings', array($this, 'restoreFormSettings')),
            new TwigFunction(
                'bs5_global_form_errors',
                null,
                array('is_safe' => array('html'), 'node_class' => 'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode')
            ),
        );
    }

    public function setStyle(string $style)
    {
        $this->style = $style;
    }

    public function getStyle(): string
    {
        return $this->style;
    }

    public function setColSize(string $colSize)
    {
        $this->colSize = $colSize;
    }

    public function getColSize(): string
    {
        return $this->colSize;
    }

    public function setWidgetCol(int $widgetCol)
    {
        $this->widgetCol = $widgetCol;
    }

    public function getWidgetCol(): int
    {
        return $this->widgetCol;
    }

    public function setLabelCol(int $labelCol)
    {
        $this->labelCol = $labelCol;
    }

    public function getLabelCol(): int
    {
        return $this->labelCol;
    }

    public function backupFormSettings()
    {
        $settings = array(
            'style'     => $this->style,
            'colSize'   => $this->colSize,
            'widgetCol' => $this->widgetCol,
            'labelCol'  => $this->labelCol,
        );

        array_push($this->settingsStack, $settings);
    }

    public function restoreFormSettings()
    {
        if (count($this->settingsStack) < 1) {
            return;
        }

        $settings = array_pop($this->settingsStack);

        $this->style     = $settings['style'];
        $this->colSize   = $settings['colSize'];
        $this->widgetCol = $settings['widgetCol'];
        $this->labelCol  = $settings['labelCol'];
    }
}