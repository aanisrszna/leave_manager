<?php

namespace Microsoft\Graph\Generated\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;
use Microsoft\Kiota\Abstractions\Store\BackedModel;
use Microsoft\Kiota\Abstractions\Store\BackingStore;
use Microsoft\Kiota\Abstractions\Store\BackingStoreFactorySingleton;

class DevicesFilter implements AdditionalDataHolder, BackedModel, Parsable 
{
    /**
     * @var BackingStore $backingStore Stores model information.
    */
    private BackingStore $backingStore;
    
    /**
     * Instantiates a new DevicesFilter and sets the default values.
    */
    public function __construct() {
        $this->backingStore = BackingStoreFactorySingleton::getInstance()->createBackingStore();
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return DevicesFilter
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): DevicesFilter {
        return new DevicesFilter();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        $val = $this->getBackingStore()->get('additionalData');
        if (is_null($val) || is_array($val)) {
            /** @var array<string, mixed>|null $val */
            return $val;
        }
        throw new \UnexpectedValueException("Invalid type found in backing store for 'additionalData'");
    }

    /**
     * Gets the BackingStore property value. Stores model information.
     * @return BackingStore
    */
    public function getBackingStore(): BackingStore {
        return $this->backingStore;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'mode' => fn(ParseNode $n) => $o->setMode($n->getEnumValue(CrossTenantAccessPolicyTargetConfigurationAccessType::class)),
            '@odata.type' => fn(ParseNode $n) => $o->setOdataType($n->getStringValue()),
            'rule' => fn(ParseNode $n) => $o->setRule($n->getStringValue()),
        ];
    }

    /**
     * Gets the mode property value. Determines whether devices that satisfy the rule should be allowed or blocked. The possible values are: allowed, blocked, unknownFutureValue.
     * @return CrossTenantAccessPolicyTargetConfigurationAccessType|null
    */
    public function getMode(): ?CrossTenantAccessPolicyTargetConfigurationAccessType {
        $val = $this->getBackingStore()->get('mode');
        if (is_null($val) || $val instanceof CrossTenantAccessPolicyTargetConfigurationAccessType) {
            return $val;
        }
        throw new \UnexpectedValueException("Invalid type found in backing store for 'mode'");
    }

    /**
     * Gets the @odata.type property value. The OdataType property
     * @return string|null
    */
    public function getOdataType(): ?string {
        $val = $this->getBackingStore()->get('odataType');
        if (is_null($val) || is_string($val)) {
            return $val;
        }
        throw new \UnexpectedValueException("Invalid type found in backing store for 'odataType'");
    }

    /**
     * Gets the rule property value. Defines the rule to filter the devices. For example, device.deviceAttribute2 -eq 'PrivilegedAccessWorkstation'.
     * @return string|null
    */
    public function getRule(): ?string {
        $val = $this->getBackingStore()->get('rule');
        if (is_null($val) || is_string($val)) {
            return $val;
        }
        throw new \UnexpectedValueException("Invalid type found in backing store for 'rule'");
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeEnumValue('mode', $this->getMode());
        $writer->writeStringValue('@odata.type', $this->getOdataType());
        $writer->writeStringValue('rule', $this->getRule());
        $writer->writeAdditionalData($this->getAdditionalData());
    }

    /**
     * Sets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @param array<string,mixed> $value Value to set for the AdditionalData property.
    */
    public function setAdditionalData(?array $value): void {
        $this->getBackingStore()->set('additionalData', $value);
    }

    /**
     * Sets the BackingStore property value. Stores model information.
     * @param BackingStore $value Value to set for the BackingStore property.
    */
    public function setBackingStore(BackingStore $value): void {
        $this->backingStore = $value;
    }

    /**
     * Sets the mode property value. Determines whether devices that satisfy the rule should be allowed or blocked. The possible values are: allowed, blocked, unknownFutureValue.
     * @param CrossTenantAccessPolicyTargetConfigurationAccessType|null $value Value to set for the mode property.
    */
    public function setMode(?CrossTenantAccessPolicyTargetConfigurationAccessType $value): void {
        $this->getBackingStore()->set('mode', $value);
    }

    /**
     * Sets the @odata.type property value. The OdataType property
     * @param string|null $value Value to set for the @odata.type property.
    */
    public function setOdataType(?string $value): void {
        $this->getBackingStore()->set('odataType', $value);
    }

    /**
     * Sets the rule property value. Defines the rule to filter the devices. For example, device.deviceAttribute2 -eq 'PrivilegedAccessWorkstation'.
     * @param string|null $value Value to set for the rule property.
    */
    public function setRule(?string $value): void {
        $this->getBackingStore()->set('rule', $value);
    }

}
