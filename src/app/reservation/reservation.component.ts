import { Component, OnInit } from '@angular/core';
import { HttpClient, HttpParams, HttpHeaders } from "@angular/common/http";
import { FormGroup, FormControl, ReactiveFormsModule, Validators } from '@angular/forms';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

@Component({
  selector: 'app-reservation',
  templateUrl: './reservation.component.html',
  styleUrls: ['./reservation.component.css']
})
export class ReservationComponent implements OnInit {
  seatAvailibilty:any[] = [];
  constructor(private http:HttpClient) { };
  headers:any = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded')
  reservationForm = new FormGroup({
    user:new FormControl('1'),
    train:new FormControl('1'),
    coach:new FormControl('1'),
    requiredSeats: new FormControl('0',[Validators.required,
      Validators.pattern("^[0-9]*$"),
      Validators.maxLength(2),Validators.min(1), Validators.max(7)])
  });
  ngOnInit() {
    this.http.post(`http://localhost/tasks/trainProblem/api.php`,{})
      .pipe(map(res => res))
      .subscribe(res=>{
        if(res['status'] == 1){
          this.seatAvailibilty = res['data'];
          console.log(this.seatAvailibilty);
        }else{
          alert(res['msg']);
        }
      });
  }
  
  searchSeat(){
    console.info(this.reservationForm.value); 
    if(this.reservationForm.value.requiredSeats <= 0 ||
      this.reservationForm.value.requiredSeats > 7){
      alert("Number of seats is required and should not be greater than 7");
      return false;
    }
    let body = new HttpParams();
    body = body.set("data",JSON.stringify(this.reservationForm.value));
    body = body.set("mode","reserve");
    this.http.post(`http://localhost/tasks/trainProblem/api.php`,body, this.headers)
      .pipe(map(res => res))
      .subscribe(res=>{
        if(res['status'] != 1){
          alert(res['msg']);
        }else{
          if(res['available'] == 0){
            alert("Required seat is not available");
          }else if(res['booked'] == 1){
            alert("Seat have been booked");
            this.seatAvailibilty = res['data'];
          }
        }
      });
  }
}
