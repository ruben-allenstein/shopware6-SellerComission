<?php declare(strict_types=1);

namespace SellerComission\Subscriber;

use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Order\OrderEvents;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderSubscriber implements EventSubscriberInterface
{
    protected LoggerInterface $logger;
    protected EntityRepositoryInterface $orderRepository;
    protected EntityRepositoryInterface $userRepository;

    public function __construct(
        LoggerInterface $logger,
        EntityRepositoryInterface $orderRepository,
        EntityRepositoryInterface $userRepository
    ) {
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderEvents::ORDER_WRITTEN_EVENT => 'orderWritten'
        ];
    }

    public function orderWritten(EntityWrittenEvent $event): void
    {

        // If API-Scope, then it is not manually created
        if ($event->getContext()->getScope() == Context::CRUD_API_SCOPE) {
            return;
        }

        // If update-event, then it is not new
        foreach ($event->getWriteResults() as $writeResult) {
            if ($writeResult->getOperation() === EntityWriteResult::OPERATION_UPDATE) {
                return;
            }
        }

        // identify logged-in admin user
        $userId = $event->getContext()->getSource()->getOriginalContext()->getSource()->getUserId();
        $user = $this->userRepository->search(new Criteria([
            $userId
        ]), $event->getContext())->first();

        $employeeNumber = null;
        $userCustomFields = $user->getCustomFields();

        if ($userCustomFields !== null && array_key_exists('employeeNumber', $userCustomFields)) {
            $employeeNumber = $userCustomFields['employeeNumber'];
        }

        if (!$employeeNumber) {
            return;
        }

        $orderIds = $event->getIds();
        $orderId = $orderIds[0];
        $order = $this->orderRepository->search(new Criteria([$orderId]), $event->getContext())->first();
        $data = [
            'id' => $orderId,
            'customFields' => [
                'employeeNumber' => $employeeNumber,
            ]
        ];

        $order = $this->orderRepository->update(
            [
                $data
            ],
            $event->getContext()
        );
    }
}
