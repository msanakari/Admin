import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { GlobalService } from '../services/global.service';

export const authGuard: CanActivateFn = (route, state) => {
  const gS = inject(GlobalService);
  const router = inject(Router);
  
  if (gS.isLoggedIn()) {
    return true;
  }
  return router.createUrlTree(['/login'], {
    queryParams: { returnUrl: state.url }
  });
};
