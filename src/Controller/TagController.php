<?php

namespace App\Controller;

use App\Repository\TagRepository;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    /**
     * @Route("/admin/tags", name="admin_tags")
     */
    public function index(TagRepository $tagRepository,Request $request, PaginatorInterface $paginator): Response
    {
        try{

        
        $usersQuery = $tagRepository->createQueryBuilder('t')
        ->getQuery();
    
        $pagination = $paginator->paginate(
            $usersQuery,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('tag/index.html.twig', [
            'pagination' => $pagination,
        ]);
        }catch(Exception $e){
            return $this->redirectToRoute('/home');
        }
    }

    /**
     * @Route("/admin/tag/{id}", name="user_admin_show_tag", methods={"GET"})
     */
    public function show(TagRepository $tagRepository, $id): Response
    {
        try{        
        $tag = $tagRepository->find($id);
        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }catch(Exception $e){
            return $this->redirectToRoute('admin_tags');
        }
    }
    /**
     * @Route("/admin/tag/{id}/edit", name="user_admin_edit_tag", methods={"GET"})
     */
    public function edit(TagRepository $tagRepository, $id): Response
    {
        $tag = $tagRepository->find($id);
        return $this->render('tag/edit.html.twig', [
            'tag' => $tag,
        ]);
    }
    /**
     * @Route("/admin/tag/{id}/edit", name="user_admin_edit_tag", methods={"POST"})
     */
    public function edit_tag(TagRepository $tagRepository, $id, Request $request): Response
    {
        try{
        $tag = $tagRepository->find($id);
        $tag->setMot($request->get('mot'));
        $tagRepository->save($tag);
        return $this->redirectToRoute('admin_tags');
        }catch(Exception $e){
            return $this->redirectToRoute('admin_tags');
        }
    }
    /**
     * @Route("/admin/tag/{id}/delete", name="user_admin_delete_tag", methods={"GET"})
     */
    public function delete(TagRepository $tagRepository, $id): Response
    {
        try{
        $tag = $tagRepository->find($id);
        $tagRepository->remove($tag);
        return $this->redirectToRoute('admin_tags');
        }catch(Exception $e){
            return $this->redirectToRoute('admin_tags');
        }
    }
    /**
     * @Route("/admin/tag/add", name="user_admin_add_tag", methods={"GET"})
     */
    public function add(): Response
    {
        return $this->render('tag/add.html.twig');
    }
    /**
     * @Route("/admin/tag/add", name="user_admin_add_tag", methods={"POST"})
     */
    public function add_tag(TagRepository $tagRepository, Request $request): Response

    {
        try{
        $tag = $tagRepository->findOneBy(['mot' => $request->get('mot')]);
        if($tag){
            return $this->redirectToRoute('admin_tags');
        }
        $tag = $tagRepository->create($request->get('mot'));
        $tagRepository->save($tag);
        return $this->redirectToRoute('admin_tags');
        }catch(Exception $e){
            return $this->redirectToRoute('admin_tags');
        }
    }
     

}
