<?php

namespace eDemy\LinkBundle\Controller;

use Symfony\Component\EventDispatcher\GenericEvent;
use eDemy\MainBundle\Controller\BaseController;
use eDemy\MainBundle\Event\ContentEvent;
use eDemy\LinkBundle\Entity\Link;
use eDemy\LinkBundle\Form\LinkType;
use eDemy\MainBundle\Entity\Param;

class LinkController extends BaseController
{
    public static function getSubscribedEvents()
    {
        return self::getSubscriptions('link', ['link'], array(
            'edemy_link_frontpage_lastmodified' => array('onFrontpageLastModified', 0),
            'edemy_link_frontpage' => array('onFrontpage', 0),
            //'edemy_agenda_actividad_details' => array('onActividadDetails', 0),
            'edemy_mainmenu'                        => array('onLinkMainMenu', 0),
        ));
    }

    public function onLinkMainMenu(GenericEvent $menuEvent) {
        $items = array();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $item = new Param($this->get('doctrine.orm.entity_manager'));
            $item->setName('Admin_Link');
            if($namespace = $this->getNamespace()) {
                $namespace .= ".";
            }
            $item->setValue($namespace . 'edemy_link_link_index');
            $items[] = $item;
        }

        $menuEvent['items'] = array_merge($menuEvent['items'], $items);

        return true;
    }

    public function onFrontpageLastModified(ContentEvent $event)
    {
        $link = $this->get('doctrine.orm.entity_manager')->getRepository('eDemyLinkBundle:Link')->findLastModified(null);
        //die(var_dump($link->getUpdated()));        
        if($link->getUpdated()) {
            $event->setLastModified($link->getUpdated());
        }

        return true;
    }

    public function onFrontpage(ContentEvent $event)
    {
//        die();
        $entities = $this->get('doctrine.orm.entity_manager')->getRepository('eDemyLinkBundle:Link')->findAll();

        //$likeurl = $this->getParam('likeurl');

        $this->addEventModule($event, 'templates/link/link', array(
            'mode' => $this->getParam('edemy_link_frontpage_mode'),
            'entities' => $entities,
            //'likeurl' => $likeurl,
        ));
    }
}
