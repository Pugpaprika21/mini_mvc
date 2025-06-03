package router

import (
	"minimvc/app/config"
	"minimvc/app/http/controllers"
	appMiddleware "minimvc/app/http/middleware"

	"minimvc/app/stdlib/jwtx"
	"minimvc/app/stdlib/views"
	"os"

	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
)

type Config struct {
	Jwtx        jwtx.IJwtx
	Controllers *controllers.Controllers
	Views       *views.Template
}

type server struct {
	server      *echo.Echo
	jwtx        *jwtx.IJwtx
	controllers controllers.Controllers
}

func New(conf *Config) *server {
	e := echo.New()

	e.Renderer = conf.Views

	e.Use(appMiddleware.LoggerWithConfig())
	e.Use(middleware.RequestID())

	for k, path := range config.Assets {
		e.Static(k, path)
	}

	server := &server{
		server:      e,
		jwtx:        &conf.Jwtx,
		controllers: *conf.Controllers,
	}

	return server
}

func (s *server) Start() {
	s.web()
	s.api()
	s.server.Logger.Fatal(s.server.Start(os.Getenv("PORT_RUN")))
}
