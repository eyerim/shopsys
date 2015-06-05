<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Model\Localization\AbstractTranslatableEntity;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Availability\Availability;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Product extends AbstractTranslatableEntity {

	const PRICE_CALCULATION_TYPE_AUTO = 'auto';
	const PRICE_CALCULATION_TYPE_MANUAL = 'manual';
	const OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY = 'setAlternateAvailability';
	const OUT_OF_STOCK_ACTION_EXCLUDE_FROM_SALE = 'excludeFromSale';
	const OUT_OF_STOCK_ACTION_HIDE = 'hide';

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductTranslation[]
	 *
	 * @Prezent\Translations(targetEntity="SS6\ShopBundle\Model\Product\ProductTranslation")
	 */
	protected $translations;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $catnum;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $partno;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $ean;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6)
	 */
	private $price;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Pricing\Vat\Vat")
	 */
	private $vat;

	/**
	 * @var \DateTime|null
	 *
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $sellingFrom;

	/**
	 * @var \DateTime|null
	 *
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $sellingTo;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $sellable;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $calculatedSellable;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $hidden;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $calculatedHidden;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $usingStock;

	/**
	 * @var int|null
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $stockQuantity;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $outOfStockAction;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Availability\Availability")
	 * @ORM\JoinColumn(name="availability_id", referencedColumnName="id", nullable=true)
	 */
	private $availability;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Availability\Availability")
	 * @ORM\JoinColumn(name="out_of_stock_availability_id", referencedColumnName="id", nullable=true)
	 */
	private $outOfStockAvailability;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Availability\Availability")
	 * @ORM\JoinColumn(name="calculated_availability_id", referencedColumnName="id", nullable=true)
	 */
	private $calculatedAvailability;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean", options={"default" = true})
	 */
	private $recalculateAvailability;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $visible;

	/**
	 * @var \SS6\ShopBundle\Model\Category\Category[]
	 *
	 * @ORM\ManyToMany(targetEntity="SS6\ShopBundle\Model\Category\Category", inversedBy="products")
	 * @ORM\JoinTable(name="product_categories")
	 */
	private $categories;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\Flag[]
	 *
	 * @ORM\ManyToMany(targetEntity="SS6\ShopBundle\Model\Product\Flag\Flag")
	 * @ORM\JoinTable(name="product_flags")
	 */
	private $flags;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=32)
	 */
	private $priceCalculationType;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product[]
	 *
	 * @ORM\ManyToMany(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 * @ORM\JoinTable(
	 *   name="product_accessories",
	 *   joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
	 *   inverseJoinColumns={@ORM\JoinColumn(name="accessory_product_id", referencedColumnName="id")}
	 * )
	 */
	private $accessories;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean", options={"default" = true})
	 */
	private $recalculatePrice;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean", options={"default" = true})
	 */
	private $recalculateVisibility;

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function __construct(ProductData $productData) {
		$this->translations = new ArrayCollection();
		$this->catnum = $productData->catnum;
		$this->partno = $productData->partno;
		$this->ean = $productData->ean;
		$this->priceCalculationType = $productData->priceCalculationType;
		if ($this->getPriceCalculationType() === self::PRICE_CALCULATION_TYPE_AUTO) {
			$this->setPrice($productData->price);
		} else {
			$this->setPrice(null);
		}
		$this->vat = $productData->vat;
		$this->sellingFrom = $productData->sellingFrom;
		$this->sellingTo = $productData->sellingTo;
		$this->sellable = $productData->sellable;
		$this->hidden = $productData->hidden;
		$this->usingStock = $productData->usingStock;
		$this->stockQuantity = $productData->stockQuantity;
		$this->outOfStockAction = $productData->outOfStockAction;
		$this->availability = $productData->availability;
		$this->outOfStockAvailability = $productData->outOfStockAvailability;
		$this->recalculateAvailability = true;
		$this->visible = false;
		$this->setTranslations($productData);
		$this->categories = $productData->categories;
		$this->flags = $productData->flags;
		$this->accessories = $productData->accessories;
		$this->recalculatePrice = true;
		$this->recalculateVisibility = true;
		$this->calculateSellableAndHidden();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function edit(ProductData $productData) {
		$this->catnum = $productData->catnum;
		$this->partno = $productData->partno;
		$this->ean = $productData->ean;
		$this->priceCalculationType = $productData->priceCalculationType;
		if ($this->getPriceCalculationType() === self::PRICE_CALCULATION_TYPE_AUTO) {
			$this->setPrice($productData->price);
		} else {
			$this->setPrice(null);
		}
		$this->vat = $productData->vat;
		$this->sellingFrom = $productData->sellingFrom;
		$this->sellingTo = $productData->sellingTo;
		$this->sellable = $productData->sellable;
		$this->usingStock = $productData->usingStock;
		$this->stockQuantity = $productData->stockQuantity;
		$this->outOfStockAction = $productData->outOfStockAction;
		$this->availability = $productData->availability;
		$this->outOfStockAvailability = $productData->outOfStockAvailability;
		$this->recalculateAvailability = true;
		$this->hidden = $productData->hidden;
		$this->setTranslations($productData);
		$this->categories = $productData->categories;
		$this->flags = $productData->flags;
		$this->accessories = $productData->accessories;
		$this->calculateSellableAndHidden();
	}

	private function calculateSellableAndHidden() {
		$this->calculatedSellable = $this->sellable;
		$this->calculatedHidden = $this->hidden;
		if ($this->isUsingStock() && $this->getStockQuantity() <= 0) {
			switch ($this->outOfStockAction) {
				case self::OUT_OF_STOCK_ACTION_HIDE:
					$this->calculatedHidden = true;
					break;
				case self::OUT_OF_STOCK_ACTION_EXCLUDE_FROM_SALE:
					$this->calculatedSellable = false;
					break;
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function changeVat(Vat $vat) {
		$this->vat = $vat;
		$this->recalculatePrice = true;
	}

	/**
	 * @param string|null $price
	 */
	public function setPrice($price) {
		$this->price = Condition::ifNull($price, 0);
	}

	/**
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string|null $locale
	 * @return string|null
	 */
	public function getName($locale = null) {
		return $this->translation($locale)->getName();
	}

	/**
	 * @return string[locale]
	 */
	public function getNames() {
		$names = [];
		foreach ($this->translations as $translation) {
			$names[$translation->getLocale()] = $translation->getName();
		}

		return $names;
	}

	/**
	 * @return string|null
	 */
	public function getCatnum() {
		return $this->catnum;
	}

	/**
	 * @return string|null
	 */
	public function getPartno() {
		return $this->partno;
	}

	/**
	 * @return string|null
	 */
	public function getEan() {
		return $this->ean;
	}

	/**
	 * @param string|null $locale
	 * @return string|null
	 */
	public function getDescription($locale = null) {
		return $this->translation($locale)->getDescription();
	}

	/**
	 * @return string
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function getVat() {
		return $this->vat;
	}

	/**
	 * @return DateTime|null
	 */
	public function getSellingFrom() {
		return $this->sellingFrom;
	}

	/**
	 * @return DateTime|null
	 */
	public function getSellingTo() {
		return $this->sellingTo;
	}

	/**
	 * @return boolean
	 */
	public function isHidden() {
		return $this->hidden;
	}

	/**
	 * @return bool
	 */
	public function getCalculatedHidden() {
		return $this->calculatedHidden;
	}

	/**
	 * @return boolean
	 */
	public function isSellable() {
		return $this->sellable;
	}

	/**
	 * @return bool
	 */
	public function getCalculatedSellable() {
		return $this->calculatedSellable;
	}

	/**
	 * @return boolean
	 */
	public function isUsingStock() {
		return $this->usingStock;
	}

	/**
	 * @return int|null
	 */
	public function getStockQuantity() {
		return $this->stockQuantity;
	}

	/**
	 * @return string
	 */
	public function getOutOfStockAction() {
		return $this->outOfStockAction;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 */
	public function getAvailability() {
		return $this->availability;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 */
	public function getOutOfStockAvailability() {
		return $this->outOfStockAvailability;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 */
	public function getCalculatedAvailability() {
		return $this->calculatedAvailability;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $availability
	 */
	public function setAvailability(Availability $availability) {
		$this->availability = $availability;
		$this->recalculateAvailability = true;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $outOfStockAvailability
	 */
	public function setOutOfStockAvailability(Availability $outOfStockAvailability) {
		$this->outOfStockAvailability = $outOfStockAvailability;
		$this->recalculateAvailability = true;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability|null $calculatedAvailability
	 */
	public function setCalculatedAvailability(Availability $calculatedAvailability = null) {
		$this->calculatedAvailability = $calculatedAvailability;
		$this->recalculateAvailability = false;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Flag\Flag[]
	 */
	public function getFlags() {
		return $this->flags;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * @return string
	 */
	public function getPriceCalculationType() {
		return $this->priceCalculationType;
	}

	/**
	 * @return boolean
	 */
	public function isVisible() {
		return $this->visible;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getAccessories() {
		return $this->accessories;
	}

	public function markPriceAsRecalculated() {
		$this->recalculatePrice = false;
	}

	public function markForVisibilityRecalculation() {
		$this->recalculateVisibility = true;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 */
	private function setTranslations(ProductData $productData) {
		foreach ($productData->name as $locale => $name) {
			$this->translation($locale)->setName($name);
		}
		foreach ($productData->description as $locale => $description) {
			$this->translation($locale)->setDescription($description);
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\ProductTranslation
	 */
	protected function createTranslation() {
		return new ProductTranslation();
	}

}
