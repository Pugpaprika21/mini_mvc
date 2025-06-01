package router

import (
	"net/http"

	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
)

func (s *server) api() {
	root := s.server
	controller := s.controllers
	_ = controller

	api := root.Group("api", middleware.CORS())

	api.GET("/health_check", func(c echo.Context) error {
		return c.JSON(http.StatusOK, echo.Map{"message": "ok"})
	})
}
