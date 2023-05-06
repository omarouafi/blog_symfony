<?php

namespace App\Controller;

use App\Entity\Tag;
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
     * @Route("/admin/tag/ajouter", name="admin_add_tag_get", methods={"GET"})
     */
    public function add(): Response
    {
        return $this->render('tag/tag-add.html.twig', [
            'mot' => '',
        ]);
        
    }
    /**
     * @Route("/admin/tag/add", name="admin_add_tag", methods={"POST"})
     */
    public function add_tag(TagRepository $tagRepository, Request $request): Response

    {
        try{
         $tag = $tagRepository->findOneBy(['mot' => $request->get('mot')]);
        if($tag){
            return $this->redirect('/admin/tags');
        }
        $tag = new Tag();
        $tag->setMot($request->get('mot'));
        $tagRepository->save($tag,true);
        return $this->redirect('/admin/tags');
        }catch(Exception $e){
            dd($e);
            return $this->redirect('/admin/tags');
        }
    }
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
            return $this->redirectToRoute('/');
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
     * @Route("/admin/tag/{id}/supprimer", name="admin_supprimer_tag", methods={"GET"})
     */
    public function supprimer_tag(Tag $tag, TagRepository $tagRepository): Response
    {
        try{
            $tagRepository->remove($tag,true);
            return $this->redirect('/admin/tags');
        } catch(Exception $e){
            return $this->redirect('/admin/tags');
        }
    }
}
