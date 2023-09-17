<?php

namespace Drupal\redirect_404\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Redirect404EventSubscriber implements EventSubscriberInterface {

  /**
   * Redirects anonymous users from 404 to login.
   *
   * @param ExceptionEvent $event
   */
  public function on404Redirect(ExceptionEvent $event) {
    // Check if the user is anonymous and the exception is a 404 error.
    if (\Drupal::currentUser()->isAnonymous() && $event->getThrowable() instanceof HttpExceptionInterface && $event->getThrowable()->getStatusCode() == 404) {
      // Redirect to the login page.
      $response = new RedirectResponse('/user/login', 302);
      $event->setResponse($response);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Listen to the kernel.exception event with a priority of -100.
    // This ensures that our subscriber runs after other subscribers.
    $events[KernelEvents::EXCEPTION][] = ['on404Redirect', -100];
    return $events;
  }
}
