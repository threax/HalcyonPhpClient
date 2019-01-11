set HERE=%~dp0
pushd %HERE%..
runsite lms 7.1 %cd% wp
popd