<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Trait\UploadedImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{

     public function __construct(private EntityManagerInterface $entity)
     {
          
     }

     #[Route('/image/store', name: 'image.store', methods:['POST'])]
     public function updateImage(Request $request)
     {
          /** @var User $user */
          $user = $this->getUser();
          $user->setImage($this->image($request->files->get('image'), $user, 'user', 'user'));
          $this->entity->flush();
          $refer = $request->headers->get('referer');
          return $this->redirect($refer);
     }

     private function image(?UploadedFile $file, User $user, string $directory = '', string $prefix = '')
     {
          if (!$file instanceof UploadedFile && $file == null && $user->getImage() == null) return null;
          if (!$file instanceof UploadedFile && $user->getImage() !== null) return $user->getImage();
          else {
               $this->deleteImage($user);
               $fileName = md5(uniqid($prefix)).'.'.$file->guessExtension();
               $file->move($this->getParameter('kernel.project_dir').'/public/image/'.$directory.'/',$fileName);
               return $directory.'/'.$fileName;
          }
     }

     private function deleteImage(User $user)
     {
          if ($user->getImage()) {
               $path = $this->getParameter('kernel.project_dir').'/public/image/'.$user->getImage();
               if (file_exists($path)) {
                    unlink($path);
               }
          }
     }

}