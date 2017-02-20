<?php

namespace AppBundle\Controller;

use AppBundle\Form\JobForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Job;
use Symfony\Component\HttpFoundation\Response;

class FunctionsController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('pages/index.html.twig');
    }

    /**
     * @Route("/approve/{email}", name="email")
     */
    public function approveAction($email)
    {
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository('AppBundle:Job')->findOneBy(array('email' => $email));
        $job->setApproved(1);
        $em->persist($job);
        $em->flush();
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/spam/{email}", name="email")
     */
    public function spam($email)
    {
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository('AppBundle:Job')->findOneBy(array('email' => $email));

        $em->remove($job);
        $em->flush();

        return $this->redirectToRoute('homepage');
    }
}
