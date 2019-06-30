create table train(
train_id int auto_increment,
train_name varchar(50),
train_no int,
primary key(train_id),
unique key(train_no)
);

create table coach(
coach_no int,
no_of_seat int(50),
primary key(coach_no)
);

create table train_coach_mapping(
train_no int,
coach_no int);

create table user_reservation(
train_no int,
coach_no int,
seat_no int,
user_id int);

Insert into train set train_name = "Dummy Train", train_no=1;
Insert into coach set coach_no=1,no_of_seat =70;
Insert into train_coach_mapping set train_no=1, coach_no = 1;
Insert into user_reservation set train_no=1,coach_no=1,seat_no=1,user_id=1;