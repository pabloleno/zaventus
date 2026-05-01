<?php

namespace App\Models;

use App\Libraries\CampoPadrao;
use CodeIgniter\Model;

abstract class PadraoModel extends Model
{
    protected $beforeInsert = ['aplicaPadraoCampos'];
    protected $beforeUpdate = ['aplicaPadraoCampos'];
    protected $beforeInsertBatch = ['aplicaPadraoCamposBatch'];
    protected $beforeUpdateBatch = ['aplicaPadraoCamposBatch'];

    protected function aplicaPadraoCampos(array $eventData): array
    {
        if (isset($eventData['data']) && is_array($eventData['data'])) {
            $eventData['data'] = CampoPadrao::normalizar($eventData['data']);
        }

        return $eventData;
    }

    protected function aplicaPadraoCamposBatch(array $eventData): array
    {
        if (isset($eventData['data']) && is_array($eventData['data'])) {
            foreach ($eventData['data'] as $indice => $linha) {
                if (is_array($linha)) {
                    $eventData['data'][$indice] = CampoPadrao::normalizar($linha);
                }
            }
        }

        return $eventData;
    }
}
