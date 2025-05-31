package router

import (
	"net/http"

	"github.com/labstack/echo/v4"
)

func (s *server) web() {
	root := s.server

	web := root.Group("web")

	user := web.Group("/user")
	{
		user.GET("/index", func(c echo.Context) error {
			data := map[string]interface{}{
				"User": "Pug",
			}
			return c.Render(http.StatusOK, "_pages/user/index.html", data)
		})
	}
}
