package home

import "github.com/labstack/echo/v4"

type IPage interface {
	Index(c echo.Context) error
}

type IHomeController interface {
	IPage
}

type homeController struct{}

func NewHomeController() IHomeController {
	return &homeController{}
}
