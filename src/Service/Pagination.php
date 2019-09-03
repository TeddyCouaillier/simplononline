<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Pagination{

    private $limit    = 10;
    private $page     = 1;
    private $criteria = [];
    private $entity;
    private $manager;
    private $twig;
    private $route;
    private $template;

    /**
     * Pagination constructor
     * @param ObjectManager $manager
     * @param Environment   $twig     Twig environment to show template
     * @param RequestStack  $request  To get the current route
     * @param string        $template Pagination's template path
     */
    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, $template)
    {
        $this->manager  = $manager;
        $this->twig     = $twig;
        $this->route    = $request->getCurrentRequest()->attributes->get('_route');
        $this->template = $template;
    }

    /**
     * Get all datas by the specific var (limit & offset)
     * @return array
     */
    public function getData()
    {
        if(empty($this->entity)){ throw new \Exception("Not entity choose for pagination"); }

        $offset = $this->page * $this->limit - $this->limit;
        $rep    = $this->manager->getRepository($this->entity);

        return $rep->findBy($this->criteria, [], $this->limit, $offset);
    }

    /**
     * Get all pages
     * @return integer
     */
    public function getPages()
    {
        if(empty($this->entity)){ throw new \Exception("Not entity choose for pagination"); }

        $rep = $this->manager->getRepository($this->entity);

        if(empty($this->criteria)){
            $total = count($rep->findAll());
        } else {
            $total = count($rep->findBy($this->criteria));
        }

        return ceil($total / $this->limit);
    }

    /**
     * Display the pagination template
     * @var boolean $empty data empty or not
     * @return Environment
     */
    public function render($empty = true)
    {
        $this->twig->display($this->template, [
            'page'  => $this->page,
            'pages' => $this->getPages(),
            'route' => $this->route,
            'empty' => $empty
        ]);
    }

    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    public function getCriteria()
    {
        return $this->criteria;
    }

    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    public function getEntity()
    {
        return $this->entity;
    }
}