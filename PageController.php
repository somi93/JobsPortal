<?php

namespace AppBundle\Controller;

use AppBundle\Form\JobForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Job;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('pages/index.html.twig');
    }

    /**
     * @Route("/newjob", name="newjob")
     */
    public function newjobAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $jobs = $em->getRepository('AppBundle:Job')->findBy(array('email' => 'pera@gmail.com'));

        $uriChar = strlen($request->getRequestUri());
        $fullUrlChar =strlen($request->getUri());
        $homeUrl = substr($request->getUri(), 0, $fullUrlChar-$uriChar);

        $number_of_jobs = count($jobs);

        $job = new Job();

        $jobForm = $this->createForm(JobForm::class, $job);
        $jobForm->handleRequest($request);

        if($jobForm->isSubmitted() && $jobForm->isValid()) {

            $title = $job->getTitle();
            $description = $job->getDescription();
            $email = $job->getEmail();
            $date = (new \DateTime());

            $job->setTitle($title);
            $job->setDescription($description);
            $job->setEmail($email);
            $job->setTime($date);

            if($number_of_jobs==0) {

                $job->setApproved(0);
                $messageUser = "
                    <html>
                    <head>
                      <title>Post Job</title>
                    </head>
                    <body>
                      <p>Your Job is posted</p>
                      <table>
                        <tr>
                          <th>Info</th>
                        </tr>
                        <tr>
                          <td>Your job is posted and your submission is in moderation</td>
                        </tr>
                      </table>
                    </body>
                    </html>
                    ";
                $headersUser  = 'MIME-Version: 1.0' . "\r\n";
                $headersUser .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headersUser .= 'To: User <$email>' . "\r\n";
                $headersUser .= 'From: Jobs Portal <jobsportal@example.com>' . "\r\n";
                mail($email, "Job Posting", $messageUser, $headersUser);

                $messageMod = "
                    <html>
                    <head>
                      <title>New Job</title>
                    </head>
                    <body>
                      <p>New Job is posted</p>
                      <table>
                        <tr>
                          <th>Title</th>
                          <th>Description</th>
                        </tr>
                        <tr>
                          <td>$title</td>
                          <td>$description</td>
                        </tr>
                        <tr>
                          <td><a href='$homeUrl/approve/$email'>Approve</a></td>
                          <td><a href='$homeUrl/spam/$email'>Mark as spam</a></td>
                        </tr>
                      </table>
                    </body>
                    </html>
                    ";
                $headersMod  = 'MIME-Version: 1.0' . "\r\n";
                $headersMod .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headersMod .= 'To: Moderator <moderator@example.com>' . "\r\n";
                $headersMod .= 'From: Jobs Portal <jobsportal@example.com>' . "\r\n";
                mail('moderator@example.com', "Job Posting", $messageMod, $headersMod);
            } else {
                $job->setApproved(1);
            }

            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }
        return $this->render('pages/newjob.html.twig', array(
            'jobForm' => $jobForm->createView()
        ));
    }
}
