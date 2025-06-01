package router

import (
	"github.com/labstack/echo/v4/middleware"
)

func (s *server) web() {
	root := s.server
	controller := s.controllers

	web := root.Group("web", middleware.CSRF())
	web.GET("/", controller.Home.Index).Name = "home.index"
}
