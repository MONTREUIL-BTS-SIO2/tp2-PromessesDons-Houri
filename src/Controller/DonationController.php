<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Form\DonationType;
use App\Repository\DonationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/donation')]
class DonationController extends AbstractController
{
    #[Route('/', name: 'app_donation_index', methods: ['GET'])]
    public function index(DonationRepository $donationRepository): Response
    {
        if ($this->getUser()){
            return $this->render('donation/index.html.twig', [
                'donations' => $donationRepository->findAll(),
            ]);
        }
        else{
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

    }
    /*
    #[Route('/new', name: 'app_donation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DonationRepository $donationRepository): Response
    {
        $donation = new Donation();
        $form = $this->createForm(DonationType::class, $donation);
        $form->handleRequest($request);
        $donation->setCreatedAt(new \DateTimeImmutable());

        if ($form->isSubmitted() && $form->isValid()) {
            $donationRepository->save($donation, true);

            return $this->redirectToRoute('app_donation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('donation/new.html.twig', [
            'donation' => $donation,
            'form' => $form,
        ]);
    }
    */

    #[Route('/{id}', name: 'app_donation_show', methods: ['GET'])]
    public function show(Donation $donation): Response
    {
        if ($this->getUser()){
            return $this->render('donation/show.html.twig', [
                'donation' => $donation,
            ]);
        }else{
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

    }

    #[Route('/{id}/edit', name: 'app_donation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Donation $donation, DonationRepository $donationRepository): Response
    {
        if ($this->getUser()){
            $form = $this->createForm(DonationType::class, $donation);
            if($donation->getHonoredAt()== null){
                $form->add('HonoredAt',DateType::class, [
                    'widget' => 'choice',
                    'input'  => 'datetime_immutable',
                    'label' => 'Date du paiement'
                ]);
            }
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $donationRepository->save($donation, true);
                return $this->redirectToRoute('app_donation_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('donation/edit.html.twig', [
                'donation' => $donation,
                'form' => $form,
            ]);

        }
        else{
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

    }

    #[Route('/{id}', name: 'app_donation_delete', methods: ['POST'])]
    public function delete(Request $request, Donation $donation, DonationRepository $donationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$donation->getId(), $request->request->get('_token'))) {
            $donationRepository->remove($donation, true);
        }

        return $this->redirectToRoute('app_donation_index', [], Response::HTTP_SEE_OTHER);
    }
}
