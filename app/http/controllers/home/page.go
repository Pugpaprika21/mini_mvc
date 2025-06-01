package home

import (
	"net/http"

	"github.com/labstack/echo/v4"
)

func (h *homeController) Index(c echo.Context) error {
	return c.Render(http.StatusOK, "_pages/home/index.html", echo.Map{
		"Content": "Golang MVC Starter",
	})
}
