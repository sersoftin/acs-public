<?php
namespace App\Controller;
use Cake\Utility\Text;

/**
 * Logs Controller
 *
 * @property \App\Model\Table\LogsTable $Logs
 */
class LogsController extends AppController
{
    public function index()
    {
        $this->log(Text::insert('Пользователь :auth_user_name (:auth_user_ip) запросил логи.', [
            'auth_user_name' => $this->Auth->user('name'),
            'auth_user_ip' => $this->request->clientIp(),
        ]), 'info', [
            'scope' => [
                'requests'
            ]
        ]);
        $logs = $this->Logs->find('all', [
            'order' => ['id' => 'DESC']
        ])->limit(100);
        $this->set(compact('logs'));
        $this->set('username', $this->Auth->user('name'));
        $this->viewBuilder()->layout('admin');
    }

    public function clear()
    {
        $this->log(Text::insert('Пользователь :auth_user_name (:auth_user_ip) очистил логи базы данных.', [
            'auth_user_name' => $this->Auth->user('name'),
            'auth_user_ip' => $this->request->clientIp()
        ]), 'info', [
            'scope' => [
                'requests'
            ]
        ]);
        if ($this->Logs->deleteAll('1=1')) {
            $this->Flash->success('Очистка логов завершена успешно.');
        } else {
            $this->Flash->error('Произошла ошибка при очистке логов.');
        }
        $this->redirect(['action' => 'index']);
    }
}
