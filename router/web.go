package router

import (
	"net/http"

	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
)

func (s *server) web() {
	root := s.server

	root.GET("/", func(c echo.Context) error {
		return c.Render(http.StatusOK, "_pages/home/index.html", echo.Map{
			"Content": "Golang MVC Starter",
		})
	}, middleware.CSRF()).Name = "home.index"
}
