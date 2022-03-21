<?php
namespace App\Http\Traits;

use Illuminate\Http\Request;
use DateTime;

trait OrdersItemsRulesTrait{
    /**
     * Apply discount rule based on the product item that was selected from the sale if you have any rule compatible with it
     *
     *
     */
    public function checkPromotionItem(&$aOrderItem)
    {
        //------------------------------------------------------------------------------------------------------------
        //no max unit limit
        $aDiscountRule = $this->DiscountRule->where('article_id', $aOrderItem->article_id)
                                            ->where('units_min', '<=' , $aOrderItem->units)
                                            ->where('units_max', -1)
                                            ->where('active', true)
                                            ->first();
        //------------------------------------------------------------------------------------------------------------
        //unit between min and max
        if (!$aDiscountRule)
            $aDiscountRule = $this->DiscountRule->where('article_id', $aOrderItem->article_id)
                                                ->where('units_min', '<=', $aOrderItem->units)
                                                ->where('units_max', '>=', $aOrderItem->units)
                                                ->where('active', true)
                                                ->first();

        if ($aDiscountRule) {
            $aOrderItem->amount_discount = ($aOrderItem->amount_liquid * $aDiscountRule->discount_percent) / 100;
            $aOrderItem->save();
        }

        //------------------------------------------------------------------------------------------------------------
        //no max amount limit
        $aDiscountRule = $this->DiscountRule->where('article_id', $aOrderItem->article_id)
                                            ->where('value_min', '<=', $aOrderItem->amount_liquid)
                                            ->where('value_max', -1)
                                            ->where('active', true)
                                            ->first();

        //------------------------------------------------------------------------------------------------------------
        //amount between min and max
        if (!$aDiscountRule) {
            $aDiscountRule = $this->DiscountRule->where('article_id', $aOrderItem->article_id)
                                                ->where('value_min', '<=', $aOrderItem->amount_liquid)
                                                ->where('value_max', '>=', $aOrderItem->amount_liquid)
                                                ->where('active', true)
                                                ->first();
        if ($aDiscountRule) {
            $aOrderItem->amount_discount = ($aOrderItem->amount_liquid * $aDiscountRule->discount_percent) / 100;
            $aOrderItem->save();
        }
        }
    }

}
