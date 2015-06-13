<?php

namespace eDemy\LinkBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class eDemyLinkBundle extends Bundle
{
    public static function getBundleName($type = null)
    {
        if ($type == null) {

            return 'eDemyLinkBundle';
        } else {
            if ($type == 'Simple') {

                return 'Link';
            } else {
                if ($type == 'simple') {

                    return 'link';
                }
            }
        }
    }

    public static function eDemyBundle() {

        return true;
    }
}
