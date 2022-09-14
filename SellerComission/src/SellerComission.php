<?php declare(strict_types=1);

namespace SellerComission;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class SellerComission extends Plugin
{
    const CUSTOM_FIELD_SET_NAME = 'SellerComission';

    public function install(InstallContext $installContext): void
    {
        $this->createCustomFieldForOrder($installContext);
    }

    protected function createCustomFieldForOrder(InstallContext $installContext): void
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        if (!$this->customFieldRelationExists($installContext)) {
            $customFieldSetRepository->create([
                [
                    'name' => self::CUSTOM_FIELD_SET_NAME,
                    'config' => [
                        'label' => [
                            'de-DE' => 'SellerComission',
                            'en-GB' => 'SellerComission'
                        ]
                    ],
                    'customFields' => [
                        [
                            'name' => 'employeeNumber',
                            'type' => 'text',
                            'config' => [
                                'componentName' => "sw-field",
                                'customFieldType' => 'text',
                                'label' => [
                                    'en-GB' => "Employee number",
                                    'de-DE' => "Verkäufernummer",
                                ],
                            ],
                        ],
                    ],
                    'relations' => [
                        ['entityName' => 'order'],
                        ['entityName' => 'user'],
                    ],
                ],
            ], $installContext->getContext());
        }
    }

    protected function customFieldRelationExists(InstallContext $installContext): bool
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $customFieldCriteria = new Criteria();
        $customFieldCriteria->addFilter(new EqualsFilter('name', self::CUSTOM_FIELD_SET_NAME));
        $customFieldCriteria->addAssociation('relations');

        $customFieldSets = $customFieldSetRepository->search($customFieldCriteria, $installContext->getContext());

        if ($customFieldSets->count() > 0) {
            return true;
        }
        return false;
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        // determine Fieldset ID
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        // Delete custom field set
        $customFieldCriteria = new Criteria();
        $customFieldCriteria->addFilter(new EqualsFilter('name', self::CUSTOM_FIELD_SET_NAME));

        $ids = $customFieldSetRepository->searchIds($customFieldCriteria, $uninstallContext->getContext())->getIds();

        if ($ids !== null) {
            foreach ($ids as $id) {
                $customFieldSetRepository->delete([['id' => $id]], $uninstallContext->getContext());
            }
        }
    }
}
