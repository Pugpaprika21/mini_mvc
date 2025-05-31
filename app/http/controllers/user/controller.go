package user

type IUserController interface{}

type userController struct{}

func NewUserController() IUserController {
	return &userController{}
}
