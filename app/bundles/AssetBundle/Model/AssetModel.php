<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic, NP. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\AssetBundle\Model;

use Mautic\CoreBundle\Entity\IpAddress;
use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\CoreBundle\Model\FormModel;
use Mautic\AssetBundle\Entity\Asset;
use Mautic\AssetBundle\AssetEvents;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Class AssetModel
 * {@inheritdoc}
 * @package Mautic\CoreBundle\Model\FormModel
 */
class AssetModel extends FormModel
{

    public function getRepository()
    {
        return $this->em->getRepository('MauticAssetBundle:Asset');
    }

    public function getPermissionBase()
    {
        return 'asset:assets';
    }

    public function getNameGetter()
    {
        return "getTitle";
    }

    /**
     * {@inheritdoc}
     *
     * @param      $entity
     * @param      $formFactory
     * @param null $action
     * @param array $options
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function createForm($entity, $formFactory, $action = null, $options = array())
    {
        if (!$entity instanceof Asset) {
            throw new MethodNotAllowedHttpException(array('Asset'));
        }
        $params = (!empty($action)) ? array('action' => $action) : array();
        return $formFactory->create('asset', $entity, $params);
    }

    /**
     * Get a specific entity or generate a new one if id is empty
     *
     * @param $id
     * @return null|object
     */
    public function getEntity($id = null)
    {
        if ($id === null) {
            $entity = new Asset();
            $entity->setSessionId('new_' . uniqid());
        } else {
            $entity = parent::getEntity($id);
            $entity->setSessionId($entity->getId());
        }

        return $entity;
    }

// TODO (@Jan): I have a feeling that following commented methods will be needed

    /**
     * {@inheritdoc}
     *
     * @param $action
     * @param $event
     * @param $entity
     * @param $isNew
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    // protected function dispatchEvent($action, &$entity, $isNew = false, $event = false)
    // {
    //     if (!$entity instanceof Page) {
    //         throw new MethodNotAllowedHttpException(array('Page'));
    //     }

    //     switch ($action) {
    //         case "pre_save":
    //             $name = PageEvents::PAGE_PRE_SAVE;
    //             break;
    //         case "post_save":
    //             $name = PageEvents::PAGE_POST_SAVE;
    //             break;
    //         case "pre_delete":
    //             $name = PageEvents::PAGE_PRE_DELETE;
    //             break;
    //         case "post_delete":
    //             $name = PageEvents::PAGE_POST_DELETE;
    //             break;
    //         default:
    //             return false;
    //     }

    //     if ($this->dispatcher->hasListeners($name)) {
    //         if (empty($event)) {
    //             $event = new PageEvent($entity, $isNew);
    //             $event->setEntityManager($this->em);
    //         }

    //         $this->dispatcher->dispatch($name, $event);
    //         return $event;
    //     } else {
    //         return false;
    //     }
    // }

    /**
     * Get list of entities for autopopulate fields
     *
     * @param $type
     * @param $filter
     * @param $limit
     * @return array
     */
    public function getLookupResults($type, $filter = '', $limit = 10)
    {
        $results = array();
        switch ($type) {
            case 'page':
                $viewOther = $this->security->isGranted('page:pages:viewother');
                $repo      = $this->getRepository();
                $repo->setCurrentUser($this->factory->getUser());
                $results = $repo->getPageList($filter, $limit, 0, $viewOther);
                break;
            case 'category':
                $results = $this->factory->getModel('page.category')->getRepository()->getCategoryList($filter, $limit, 0);
                break;
        }

        return $results;
    }

    /**
     * Generate url for a page
     *
     * @param $entity
     * @param $absolute
     * @return mixed
     */
    // public function generateUrl($entity, $absolute = true)
    // {
    //     $pageSlug = $entity->getId() . ':' . $entity->getAlias();

    //     //should the url include the category
    //     $catInUrl    = $this->factory->getParameter('cat_in_page_url');
    //     if ($catInUrl) {
    //         $category = $entity->getCategory();
    //         $catSlug = (!empty($category)) ? $category->getId() . ':' . $category->getAlias() :
    //             $this->translator->trans('mautic.core.url.uncategorized');
    //     }

    //     $parent = $entity->getTranslationParent();
    //     if ($parent) {
    //         //multiple languages so tak on the language
    //         $slugs = array(
    //             'slug1' => $entity->getLanguage(),
    //             'slug2' => (!empty($catSlug)) ? $catSlug : $pageSlug,
    //             'slug3' => (!empty($catSlug)) ? $pageSlug : ''
    //         );
    //     } else {
    //         $slugs = array(
    //             'slug1' => (!empty($catSlug)) ? $catSlug : $pageSlug,
    //             'slug2' => (!empty($catSlug)) ? $pageSlug : '',
    //             'slug3' => ''
    //         );
    //     }

    //     $pageUrl  = $this->factory->getRouter()->generate('mautic_page_public', $slugs, $absolute);

    //     return $pageUrl;
    // }
}