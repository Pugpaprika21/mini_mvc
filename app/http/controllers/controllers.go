package controllers

import (
	"minimvc/app/http/controllers/user"
	"minimvc/app/http/services"
)

type IControllers interface {
	services.IServices
}

type controllers struct {
	User user.IUserController
}

func New(services services.IServices) IControllers {
	return &controllers{
		User: user.NewUserController(),
	}
}
