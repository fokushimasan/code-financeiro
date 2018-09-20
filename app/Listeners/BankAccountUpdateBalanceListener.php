<?php

namespace CodeFin\Listeners;

use CodeFin\Events\BillStoredEvent;
use CodeFin\Repositories\BankAccountRepository;
use CodeFin\Repositories\StatementRepository;
use Prettus\Repository\Events\RepositoryEventBase;

class BankAccountUpdateBalanceListener
{
    private $bankAccountRepository;
    private $statementRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(BankAccountRepository $bankAccountRepository, StatementRepository $statementRepository)
    {
        $this->bankAccountRepository = $bankAccountRepository;
        $this->bankAccountRepository->skipPresenter(true);
        $this->statementRepository = $statementRepository;
    }

    /**
     * Handle the event.
     *
     * @param  RepositoryEventBase  $event
     * @return void
     */
    public function handle(BillStoredEvent $event)
    {
        $model = $event->getModel();
        $bankAccountId = $model->bank_account_id;
        $value = $this->getValue($event);

        if ($value){
            $bankAccount = $this->bankAccountRepository->addBalance($bankAccountId,$value);
            $this->statementRepository->create([
                'statementable' => $model,
                'value' => $value,
                'balance' => $bankAccount->balance,
                'bank_account_id' => $bankAccount->id
            ]);
        }
    }

    protected function getValue(BillStoredEvent $event)
    {

    }
}
