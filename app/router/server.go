package router

import (
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
	Controllers controllers.IControllers
	Views       *views.Template
}

type server struct {
	server      *echo.Echo
	jwtx        jwtx.IJwtx
	controllers controllers.IControllers
}

func New(conf *Config) *server {
	e := echo.New()

	e.Renderer = conf.Views

	e.Use(appMiddleware.LoggerWithConfig())
	e.Use(middleware.RequestID())

	e.Static("css", "../public/css")
	e.Static("js", "../public/js")

	server := &server{
		server:      e,
		jwtx:        conf.Jwtx,
		controllers: conf.Controllers,
	}

	return server
}

func (s *server) Start() {
	s.web()
	s.api()
	s.server.Logger.Fatal(s.server.Start(os.Getenv("PORT_RUN")))
}
