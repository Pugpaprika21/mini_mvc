package controllers

import (
	"minimvc/app/http/controllers/home"
	"minimvc/app/http/controllers/user"
	"minimvc/app/http/services"
)

type Controllers struct {
	Home home.IHomeController
	User user.IUserController
}

func New(services services.IServices) *Controllers {
	return &Controllers{
		User: user.NewUserController(),
		Home: home.NewHomeController(),
	}
}
