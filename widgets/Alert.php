<?php

namespace app\widgets;

use yii\bootstrap5\Alert as BaseAlert;

/**
 * Alert widget — encapsula o componente Bootstrap 5 Alert do Yii2
 * e exibe automaticamente as mensagens de flash da sessão.
 *
 * Uso nos layouts:
 *   echo Alert::widget();
 */
class Alert extends \yii\base\Widget
{
    public function run(): string
    {
        $session = \Yii::$app->session;
        $flashes = $session->getAllFlashes();
        $output  = '';

        $map = [
            'success' => 'success',
            'error'   => 'danger',
            'warning' => 'warning',
            'info'    => 'info',
        ];

        foreach ($flashes as $type => $messages) {
            $bsClass = $map[$type] ?? 'info';
            if (!is_array($messages)) {
                $messages = [$messages];
            }
            foreach ($messages as $message) {
                $output .= BaseAlert::widget([
                    'options' => [
                        'class' => "alert-{$bsClass} alert-dismissible",
                    ],
                    'closeButton' => [
                        'class' => 'btn-close',
                    ],
                    'body' => \yii\helpers\Html::encode($message),
                ]);
            }
        }

        return $output;
    }
}