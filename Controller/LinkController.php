<?php

namespace eDemy\LinkBundle\Controller;

use eDemy\MainBundle\Controller\BaseController;
use eDemy\MainBundle\Event\ContentEvent;

use eDemy\LinkBundle\Entity\Link;
use eDemy\LinkBundle\Form\LinkType;

class LinkController extends BaseController
{
    public static function getSubscribedEvents()
    {
        return self::getSubscriptions('link', ['link'], array(
            'edemy_link_frontpage_lastmodified' => array('onFrontpageLastModified', 0),
            'edemy_link_frontpage' => array('onFrontpage', 0),
            //'edemy_agenda_actividad_details' => array('onActividadDetails', 0),
        ));
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

        $this->addEventModule($event, 'templates/link', array(
            'mode' => $this->getParam('edemy_link_frontpage_mode'),
            'entities' => $entities,
            //'likeurl' => $likeurl,
        ));
    }
}
