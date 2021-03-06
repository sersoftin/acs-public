<?php
namespace App\Controller\ClientApi;

use App\Model\Entity\Bid;
use App\Model\Entity\Pc;
use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\NotFoundException;

/**
 * Bids Controller
 *
 * @property \App\Model\Table\BidsTable $Bids
 */
class BidsController extends AppController
{
    public function add()
    {
        $this->request->allowMethod('post');

        $pc_unique_key = $this->request->data('pc_unique_key');
        $pc_name = $this->request->data('pc_name');
        $product_id = $this->request->data('product_id');

        $pc = $this->Bids->Pcs->findOrCreate(['unique_key' => $pc_unique_key],
            function (Pc $pc) use ($pc_name) {
                $pc->client_id = 0;
                $pc->name = $pc_name;
                $pc->addition_date = Time::now();
                $pc->comment = '';
            });

        $bid = $this->Bids->findOrCreate([
            'product_id' => $product_id,
            'pc_id' => $pc->id
        ], function (Bid $bid) {
            $bid->application_date = Time::now();
            $bid->is_active = false;
        });

        if (!$bid->has('id')) {
            throw new BadRequestException('Bid not saved. Invalid data accepted.');
        }

        $this->set(compact('bid'));
    }

    public function check()
    {
        $this->request->allowMethod('post');

        $pc_unique_key = $this->request->data('pc_unique_key');
        $product_id = $this->request->data('product_id');

        $bid = $this->Bids->find('all', [
            'conditions' => [
                'Pcs.unique_key' => $pc_unique_key,
                'product_id' => $product_id
            ],
            'contain' => ['Pcs']
        ])->first();
        if (empty($bid)) {
            throw new NotFoundException('Bid not found.');
        } else {
            $bid->unsetProperty('pc');
        }

        $this->set(compact('bid'));
    }
}