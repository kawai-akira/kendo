<?php

namespace Plugin\EccubePaymentLite42\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;
use Eccube\Entity\Product;

/**
 * @EntityExtension("Eccube\Entity\Product")
 */
trait ProductTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="free_description_about_quantity", type="text", nullable=true)
     */
    private $free_description_about_quantity;
    /**
     * @var string|null
     *
     * @ORM\Column(name="free_description_about_selling_price", type="text", nullable=true)
     */
    private $free_description_about_selling_price;
    /**
     * @var string|null
     *
     * @ORM\Column(name="free_description_of_payment_delivery", type="text", nullable=true)
     */
    private $free_description_of_payment_delivery;

    /**
     * Set Free Description About Quantity.
     *
     * @param string|null $free_description_about_quantity
     *
     * @return Product
     */
    public function setFreeDescriptionAboutQuantity($free_description_about_quantity = null)
    {
        $this->free_description_about_quantity = $free_description_about_quantity;

        return $this;
    }

    /**
     * Get Free Description About Quantity.
     *
     * @return string|null
     */
    public function getFreeDescriptionAboutQuantity()
    {
        return $this->free_description_about_quantity;
    }

    /**
     * Set Free Description About Selling Price.
     *
     * @param string|null $free_description_about_selling_price
     *
     * @return Product
     */
    public function setFreeDescriptionAboutSellingPrice($free_description_about_selling_price = null)
    {
        $this->free_description_about_selling_price = $free_description_about_selling_price;

        return $this;
    }

    /**
     * Get Free Description About Selling Price.
     *
     * @return string|null
     */
    public function getFreeDescriptionAboutSellingPrice()
    {
        return $this->free_description_about_selling_price;
    }

    /**
     * Set Free Description Of Payment Delivery.
     *
     * @param string|null $free_description_of_payment_delivery
     *
     * @return Product
     */
    public function setFreeDescriptionOfPaymentDelivery($free_description_of_payment_delivery = null)
    {
        $this->free_description_of_payment_delivery = $free_description_of_payment_delivery;

        return $this;
    }

    /**
     * Get Free Description Of Payment Delivery.
     *
     * @return string|null
     */
    public function getFreeDescriptionOfPaymentDelivery()
    {
        return $this->free_description_of_payment_delivery;
    }

    
    public function hasCategorySet()
    {
        foreach ($this->ProductCategories as $productCategory) {
            if ($productCategory->getCategoryId() === \Eccube\Entity\Category::SET) {
                return true;
            }
        }
        return false;
    }

    public function hasCategoryMen()
    {
        foreach ($this->ProductCategories as $productCategory) {
            if ($productCategory->getCategoryId() === \Eccube\Entity\Category::MEN) {
                return true;
            }
        }
        return false;
    }

    public function hasCategoryKote()
    {
        foreach ($this->ProductCategories as $productCategory) {
            if ($productCategory->getCategoryId() === \Eccube\Entity\Category::KOTE) {
                return true;
            }
        }
        return false;
    }

    public function hasCategoryDou()
    {
        foreach ($this->ProductCategories as $productCategory) {
            if ($productCategory->getCategoryId() === \Eccube\Entity\Category::DOU) {
                return true;
            }
        }
        return false;
    }

    public function hasCategoryTare()
    {
        foreach ($this->ProductCategories as $productCategory) {
            if ($productCategory->getCategoryId() === \Eccube\Entity\Category::TARE) {
                return true;
            }
        }
        return false;
    }

    public function hasCategoryDoui()
    {
        foreach ($this->ProductCategories as $productCategory) {
            if ($productCategory->getCategoryId() === \Eccube\Entity\Category::DOUI) {
                return true;
            }
        }
        return false;
    }

    public function hasCategoryHakama()
    {
        foreach ($this->ProductCategories as $productCategory) {
            if ($productCategory->getCategoryId() === \Eccube\Entity\Category::HAKAMA) {
                return true;
            }
        }
        return false;
    }

    public function hasCategoryShinai()
    {
        foreach ($this->ProductCategories as $productCategory) {
            if ($productCategory->getCategoryId() === \Eccube\Entity\Category::SHINAI) {
                return true;
            }
        }
        return false;
    }

    public function hasCategoryZekken()
    {
        foreach ($this->ProductCategories as $productCategory) {
            if ($productCategory->getCategoryId() === \Eccube\Entity\Category::ZEKKEN) {
                return true;
            }
        }
        return false;
    }

}
