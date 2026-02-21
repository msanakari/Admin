import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './component/login/login.component';
import { DashBoardLayoutComponent } from './component/dash-board-layout/dash-board-layout.component';
import { DashboardComponent } from './component/dashboard/dashboard.component';
import { BuyComponent } from './component/buy/buy.component';
import { SellComponent } from './component/sell/sell.component';
import { SellBuyComponent } from './component/sell-buy/sell-buy.component';
import { AgentsComponent } from './component/agents/agents.component';
import { authGuard } from './guards/auth.guard';
import { NotFoundComponent } from './component/not-found/not-found.component';
import { SettingsComponent } from './component/settings/settings.component';
import { PropertyListingComponent } from './component/property-listing/property-listing.component';
import { PropertyEnquiryComponent } from './component/property-enquiry/property-enquiry.component';

const routes: Routes = [
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full'
  },
  {
    path: 'login',
    component: LoginComponent
  },
  {
    path: '',
    component: DashBoardLayoutComponent,
    canActivate: [authGuard],
    children: [
      {
        path: 'dashboard',
        component: DashboardComponent,
        title: 'dashboard'
      },
      {
        path: 'buy',
        component: BuyComponent,
        title: 'Buy'
      },
      {
        path: 'sell',
        component: SellComponent,
        title: 'Sell'
      },
      {
        path: 'sell&Buy',
        component: SellBuyComponent,
        title: 'Sell and Buy'
      },
      {
        path: 'agents',
        component: AgentsComponent,
        title: 'Agents'
      },
      {
        path: 'listing',
        component: PropertyListingComponent,
        title: 'Listing'
      },

      {
        path: 'property-enquiry',
        component: PropertyEnquiryComponent,
        title: 'property-enquiry'
      },
      {
        path: 'settings',
        component: SettingsComponent,
        title: 'Agents'
      }


    ]
  },
  { path: '**', component: NotFoundComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes, {
    preloadingStrategy: PreloadAllModules
  })],
  exports: [RouterModule]
})
export class AppRoutingModule { }
