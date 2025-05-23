<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsername($form->get('username')->getData());
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setEmail($form->get('userEmail')->getData());
            try
            {
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Користувач успішно збережений!');
            }
            catch (\Exception $e)
            {
                if ($e->getCode() == 1062)
                {
                    // $this->addFlash('danger', 'Виникла помилка при збереженні користувача: ' . $e->getMessage());
                    // $this->addFlash('danger', 'Виникла помилка при збереженні користувача: ' . $e->getCode());
                    // $this->addFlash('danger', 'Користувач ' . ($e->getCode() == 1062 ? '"' . $form->get('username')->getData() . '" вже існує.' : ''));
                    // $this->addFlash('success', 'Дані були успішно збережені!');
                    // $this->addFlash('info', 'Будь ласка, зверніть увагу на це повідомлення.');
                    // $this->addFlash('warning', 'Деякі поля були заповнені некоректно.');
                    // $this->addFlash('danger', 'Виникла помилка під час обробки запиту.');
                    $this->addFlash('danger', 'Користувач ' . $form->get('username')->getData() . '" вже існує.');
                    return $this->redirectToRoute('app_register');
                }
                else
                {
                    throw new \Exception($e->getMessage(), $e->getCode(), $e);
                }
            }

            // generate a signed url and email it to the user
// dd($this);
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('mail1@we.lc', 'My Mail Bot'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//dd($this);
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_reg_success');
    }
}
