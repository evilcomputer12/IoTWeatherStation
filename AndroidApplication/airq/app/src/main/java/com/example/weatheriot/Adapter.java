package com.example.weatheriot;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;


import java.util.List;

public class Adapter extends RecyclerView.Adapter<Adapter.ViewHolder> {
    LayoutInflater inflater;
    List<lista> listi;

    public Adapter(Context ctx, List<lista> listi){
        this.inflater = LayoutInflater.from(ctx);
        this.listi = listi;
    }




    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = inflater.inflate(R.layout.item,parent,false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        // bind the data
        holder.id1.setText(listi.get(position).getId());
        holder.id2.setText(listi.get(position).getTemp());
        holder.id3.setText(listi.get(position).getHum());
        holder.id4.setText(listi.get(position).getPm10());
        holder.id5.setText(listi.get(position).getPm25());
        holder.id6.setText(listi.get(position).getVreme());
        holder.id8.setText(listi.get(position).getBat_vol());
        holder.id9.setText(listi.get(position).getSol_vol());
        holder.id6.setText(listi.get(position).getVreme());

    }

    @Override
    public int getItemCount() {
        return listi.size();
    }

    public  class ViewHolder extends  RecyclerView.ViewHolder{
        TextView id1,id2,id3,id4,id5,id6, id8, id9;

        public ViewHolder(@NonNull View itemView) {
            super(itemView);

            id1 = itemView.findViewById(R.id.id1);
            id2 = itemView.findViewById(R.id.id2);
            id3 = itemView.findViewById(R.id.id3);
            id4 = itemView.findViewById(R.id.id4);
            id5 = itemView.findViewById(R.id.id5);
            id8 = itemView.findViewById(R.id.id8);
            id9 = itemView.findViewById(R.id.id9);
            id6 = itemView.findViewById(R.id.id6);


            itemView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    Toast.makeText(v.getContext(), "Do Something With this Click", Toast.LENGTH_SHORT).show();
                }
            });
        }
    }
}